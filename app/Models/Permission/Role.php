<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Modules\Manage\Models\Menu;

class Role extends Model
{
    protected $table = "admin_roles";
    protected $table_admin_permissions = "admin_permissions";
    public $timestamps = false;
    
    public function getRoleLists()
    {
        return self::get();
    }
    
    public function getRoleMenuIdArr($roleId)
    {
        return DB::table($this->table_admin_permissions)->where("role_id", $roleId)->get();
    }
    
    //判断指定用户组是否有后台管理权限
    public function getIsAuth($roleId)
    {
        $menuAuth = DB::table($this->table_admin_permissions)->where("role_id", $roleId)->count();
        if (!$menuAuth) {
            return false;
        } else {
            return true;
        }
    }
    
    //判断指定菜单和指定操作是否有权限
    public function getIsMenuAndActionAuth($roleId, $menuPath, $actionPath)
    {
        //获取当前菜单信息
        $menu = new Menu();
        $menuInfo = $menu->getMenuInfoFromPath("/" . $menuPath);
        if (isset($menuInfo->id)) {
            //判断当前操作是否有授权
            $roleInfo = DB::table($this->table_admin_permissions)->where("role_id", $roleId)->where("menu_id",
                $menuInfo->id)->select(['action_ids'])->first();
            if ($actionPath) {
                $roleAuthArray = json_decode($roleInfo->action_ids);
                return $menu->getMenuIsAuthFromPath("/" . $menuPath . "/" . $actionPath, $roleAuthArray);
            }
            return true;
        } else {
            return true;
        }
    }
    
    //获取当前菜单下所有子菜单是否全部选中
    public function getMenuChildIsChecked($pid, $roleId)
    {
        $menu = new Menu();
        $childIdArr = $menu->where("parent_id", $pid)->pluck("id");
        //获取子菜单下操作
        $childTotal = count($childIdArr);
        //获取已被选中的菜单数量
        if ($childTotal) {
            $checkNum = DB::table($this->table_admin_permissions)->whereIn("menu_id", $childIdArr)->where("role_id",
                $roleId)->count();
            if ($checkNum == $childTotal) {
                return true;
            }
        } else {
            $role = new Role();
            $childActionIdTotal = $menu->getMenuActionCount($pid);
            $checkNum = count($role->getRoleMenuActionArr($pid, $roleId));
            if ($childActionIdTotal == $checkNum) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    //获取指定菜单下操作授权ID
    public function getRoleMenuActionLists($menuId)
    {
        return DB::table($this->table_admin_permissions)->where("menu_id", $menuId)->get();
    }
    
    //获取菜单下操作授权ID,返回数组
    public function getRoleMenuActionArr($menuId, $roleId)
    {
        $roleMenuActionArr = DB::table($this->table_admin_permissions)->where("menu_id", $menuId)->where("role_id",
            $roleId)->pluck("action_ids");
        if (isset($roleMenuActionArr[0])) {
            return json_decode($roleMenuActionArr[0]);
        } else {
            return array();
        }
    }
    
    //获取用户组名称
    public function getRoleName($roleId)
    {
        $roleNameArr = self::where("id", $roleId)->pluck("name");
        return isset($roleNameArr[0]) ? $roleNameArr[0] : "";
    }
    
    //更新授权操作
    public function updateMenuAction($roleId, $menuId, $actionIds)
    {
        return DB::table($this->table_admin_permissions)->where("role_id", $roleId)->where("menu_id",
            $menuId)->update(["action_ids" => $actionIds]);
    }
    
    //添加管理员组
    public function add($request)
    {
        $addData = array(
            "name" => htmlspecialchars($request->rolename),
            "remark" => htmlspecialchars($request->remark),
            "create_date" => date("Y-m-d H:i:s"),
            "update_date" => date("Y-m-d H:i:s")
        );
        $lasrtId = self::insertGetId($addData);
        //更新排序
        //self::where("id","=",$lasrtId)->update(["sort"=>$lasrtId]);
        return returnData("添加成功！", 1);
    }
    
    //编辑角色
    public function roleUpdate($request)
    {
        $updateData = array(
            "name" => htmlspecialchars($request->rolename),
            "remark" => htmlspecialchars($request->remark),
            "update_date" => date("Y-m-d H:i:s"),
        );
        self::where("id", "=", $request->id)->update($updateData);
        //更新排序
        return returnData("更新成功！", 1);
    }
    
    //添加菜单授权
    public function addAuthMenu($menuId)
    {
        $addData = array(
            "menu_id" => $menuId,
            "action_ids" => json_encode(array()),
            "role_id" => 1,
        );
        return DB::table($this->table_admin_permissions)->insertGetId($addData);
    }
    
    //删除菜单角色
    public function deleteAuthMenu($menuId)
    {
        return DB::table($this->table_admin_permissions)->where("menu_id", $menuId)->delete();
    }
    
    //添加区域授权
    public function addAuthRegion($regionId)
    {
        $addData = array(
            "region_id" => $regionId,
            "role_id" => 1,
        );
        return DB::table($this->table_admin_region_permissions)->insertGetId($addData);
    }
    
    //删除区域授权
    public function deleteAuthRegion($regionId)
    {
        return DB::table($this->table_admin_region_permissions)->where("region_id", $regionId)->delete();
    }
    
    //删除角色
    public function roleDelete($roleId)
    {
        $res = self::where("id", "=", $roleId)->delete();
        if (!$res) {
            return returnData("删除失败！", 0);
        } else {
            return returnData("删除成功！", 1);
        }
    }
    
    //编辑授权组
    public function saveRoleAuth($request)
    {
        try {
            //保存菜单权限
            DB::table($this->table_admin_permissions)->where("role_id", $request->roles_id)->delete();
            if (count($request->menu_id)) {
                $menuActionIdArr = $request->menu_action_id;
                foreach ($request->menu_id as $key => $val) {
                    $addAuth = array(
                        "menu_id" => $val,
                        "action_ids" => isset($menuActionIdArr[$val]) && is_array($menuActionIdArr[$val]) ? json_encode($menuActionIdArr[$val]) : json_encode(array()),
                        "role_id" => $request->roles_id
                    );
                    DB::table($this->table_admin_permissions)->insertGetId($addAuth);
                }
            }
            return returnData("修改成功！", 1);
        } catch (\Exception $e) {
            return returnData($e->getMessage());
        }
    }
    
    static function isMenuAction($actionKey)
    {
        if (!actionIsView($actionKey)) {
            return returnData("操作未授权");
        } else {
            return returnData("授权成功", 1);
        }
    }
}
