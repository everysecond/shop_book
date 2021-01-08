<?php

namespace Modules\Manage\Models;

use App\Models\Permission\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Menu extends Model
{
    protected $table = "admin_menus";
    protected $table_menu_action = "admin_menu_actions";
    public $timestamps = false;
    
    public function add($request)
    {
        $addData = array(
            "title" => htmlspecialchars($request->title),
            "menu_label" => uniqid(),
            "url" => $request->url,
            "icon_class" => htmlspecialchars($request->icon_class),
            "parent_id" => intval($request->parent_id),
            "sort" => 0,
            "create_date" => date("Y-m-d H:i:s"),
            "update_date" => date("Y-m-d H:i:s"),
        );
        $lasrtId = self::insertGetId($addData);
        if ($lasrtId) {
            //更新排序
            self::where("id", "=", $lasrtId)->update(["sort" => $lasrtId]);
            $role = new Role();
            $role->addAuthMenu($lasrtId);
            self::where("id", "=", $lasrtId)->update(["sort" => $lasrtId]); //为管理员添加管理权限
            return returnData("添加成功！", 1);
        } else {
            return returnData("添加失败！");
        }
    }
    
    //更新菜单状态
    public function upMenuStatus($menuId, $status)
    {
        $res = self::where("id", "=", $menuId)->update(['is_show' => $status]);
        if (!$res) {
            return returnData("更新失败！");
        } else {
            return returnData("更新成功！", 1);
        }
    }
    
    //编辑菜单
    public function menuUpdate($request)
    {
        $updateData = array(
            "title" => htmlspecialchars($request->title),
            "url" => $request->url,
            "icon_class" => htmlspecialchars($request->icon_class),
            "parent_id" => intval($request->parent_id),
            "create_date" => date("Y-m-d H:i:s"),
            "update_date" => date("Y-m-d H:i:s"),
        );
        self::where("id", "=", $request->id)->update($updateData);
        //更新排序
        return returnData("更新成功！", 1);
    }
    
    //更新菜单排序
    public function updateMenuSort(array $sortArray = array())
    {
        foreach ($sortArray as $key => $data) {
            self::where('id', $key)->update(['sort' => $data]);
        }
        return returnData('排序更新成功', 1);
    }
    
    //验证是否有子菜单
    public function checkIsChild($menuId)
    {
        $childNum = self::where("parent_id", "=", $menuId)->count();
        if (!$childNum) {
            return returnData("无子菜单", 1);
        } else {
            return returnData("有子菜单", 0);
        }
    }
    
    //验证菜单下指定操作是否授权
    public function getMenuActionIsAuth($actionKey)
    {
        $actionInfo = DB::table($this->table_menu_action)->where("key", $actionKey)->select(array(
            "id",
            "menu_id"
        ))->first();
        if (isset($actionInfo->id)) {
            //获取当前操作是否授权
            $role = new Role();
            $adminInfo = Session::get("adminInfo");
            $menuAuthAction = $role->getRoleMenuActionArr($actionInfo->menu_id, $adminInfo->role_id);
            if (is_array($menuAuthAction) && in_array($actionInfo->id, $menuAuthAction)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    //删除菜单
    public function menuDelete($menuId)
    {
        DB::beginTransaction();
        try {
            $res = self::where("id", "=", $menuId)->delete();
            if (!$res) {
                throw new \Exception('删除菜单失败');
            } else {
                //删除菜单下操作
                DB::table($this->table_menu_action)->where("menu_id", $menuId)->delete();
                $role = new Role();
                $role->deleteAuthMenu($menuId);
            }
            DB::commit();
            return returnData("删除成功！", 1);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnData("删除失败|" . $e->getMessage(), 0);
        }
    }
    
    public function addAction($request)
    {
        $addData = array(
            "menu_id" => $request->menu_id,
            "name" => $request->name,
            "key" => $request->key,
            "path" => $request->path,
            "create_date" => date("Y-m-d H:i:s")
        );
        $lastId = DB::table($this->table_menu_action)->insertGetId($addData);
        if ($lastId) {
            //添加成功后加入管理员授权
            $role = new Role();
            $menuRoleActionIdArr = $role->getRoleMenuActionArr($addData['menu_id'], 1);
            if (count($menuRoleActionIdArr) && $menuRoleActionIdArr) {
                $newMenuRoleActionIdArr = array_merge(array($lastId), $menuRoleActionIdArr);
            } else {
                $newMenuRoleActionIdArr = array($lastId);
            }
            //更新授权信息
            $role->updateMenuAction(1, $addData['menu_id'], json_encode($newMenuRoleActionIdArr));
            $data['id'] = $lastId;
            $data['name'] = $request->name;
            return returnData("添加成功", 1, $data);
        } else {
            return returnData("添加失败");
        }
    }
    
    //删除菜单操作
    public function deleteAction($menuId, $actionIds)
    {
        DB::beginTransaction();
        try {
            $res = DB::table($this->table_menu_action)->where("menu_id", $menuId)->whereIn("id", $actionIds)->delete();
            if (!$res) {
                throw new \Exception('操作删除失败');
            }
            //从授权表中删除授权信息
            $role = new Role();
            $actionAuthLists = $role->getRoleMenuActionLists($menuId);
            foreach ($actionIds as $actid) {
                if (count($actionAuthLists)) {
                    foreach ($actionAuthLists as $val) {
                        $actionAuthIdArr = json_decode($val->action_ids);
                        if (is_array($actionAuthIdArr)) {
                            if (in_array($actid, $actionAuthIdArr)) {
                                $updateAuthActionId = json_encode(array_diff($actionAuthIdArr, [$actid]));
                                $role->updateMenuAction($menuId, $val->role_id, $updateAuthActionId);
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return returnData($e->getMessage() . "|" . $e->getFile() . "|" . $e->getLine());
        }
        return returnData("删除成功", 1);
    }
    
    //验证菜单下指定操作是否存在
    public function checkMenuActionIsExists($menuId, $menuActionName)
    {
        return DB::table($this->table_menu_action)->where("menu_id", $menuId)->where("name", $menuActionName)->count();
    }
    
    //获取全部菜单列表
    public function getMenuList()
    {
        $listParent = self::where("parent_id", "=", 0)->orderBy('sort', 'ASC')->get();
        $lists = array();
        foreach ($listParent as $key => $val) {
            $child = self::where("parent_id", "=", $val->id)->orderBy('sort', 'ASC')->get();
            $val->child = $child;
        }
        return $listParent;
    }
    
    //获取顶级菜单列表,1为获取管理列表，2获取显示列表
    public function getFirstMenu($type = 1)
    {
        if ($type == 1) {
            return self::where("parent_id", "=", 0)->orderBy("sort", "ASC")->get();
        } elseif ($type == 2) {
            return self::where("parent_id", "=", 0)->where("is_show", "=", 1)->orderBy("sort", "ASC")->get();
        }
    }
    
    //获取顶级菜单列表,1为获取管理列表，2获取显示列表
    public function getTwoMenu($parentId, $type = 1)
    {
        if ($type == 1) {
            return self::where("parent_id", "=", $parentId)->get();
        } elseif ($type == 2) {
            return self::where("parent_id", "=", $parentId)->where("is_show", "=", 1)->orderBy("sort")->get();
        }
    }
    
    //获取菜单下操作
    public function getMenuAction($menuId)
    {
        return DB::table($this->table_menu_action)->where("menu_id", $menuId)->get();
    }
    
    //获取指定菜单下操作数量
    public function getMenuActionCount($menuId)
    {
        return DB::table($this->table_menu_action)->where("menu_id", $menuId)->count();
    }
    
    public function getMenuInfo($action, $type = 1)
    {
        if ($type == 1) {
            return self::where("id", "=", $action)->first();
        } elseif ($type == 2) {
            return self::where("url", "=", $action)->first();
        } elseif ($type == 3) {
            return self::where("id", "=", $action)->first();
        }
    }
    
    //通过菜单path获取菜单信息
    public function getMenuInfoFromPath($path, $fields = array())
    {
        if ($fields) {
            return self::where("url", $path)->select($fields)->first();
        } else {
            return self::where("url", $path)->first();
        }
    }
    
    //通过菜单操作路径获取操作是否授权
    public function getMenuIsAuthFromPath($path, $roleAuthArray)
    {
        $menuAction = DB::table($this->table_menu_action)->where("path", $path)->select(['id'])->first();
        if (!isset($menuAction->id)) {
            return true;
        } else {
            if (in_array($menuAction->id, $roleAuthArray)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
