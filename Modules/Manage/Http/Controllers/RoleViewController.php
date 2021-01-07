<?php

namespace Modules\Manage\Http\Controllers;

use App\Models\Permission\Role;
use Modules\Manage\Models\Menu;
use App\Classlib\FormCheck;
use Illuminate\Http\Request;


class RoleViewController extends Controller
{
    public function __construct()
    {
        $this->formCheck = new FormCheck();
        $this->role = new Role();
    }
    
    public function index(Request $request)
    {
        $roleLists = $this->role->getRoleLists();
        $menuInfo = getMenuFromPath($request->path());
        return view("manage::role.role")
            ->with("roleLists", $roleLists)
            ->with("title", $menuInfo->title)
            ->with("thisAction", $menuInfo->url);
    }
    
    //授权管理
    public function roleAuth(Request $request, $id)
    {
        //获取一级菜单
        $menu = new Menu();
        $role = new Role();
        $menuLists = $menu->getFirstMenu(2);
        //获取下级菜单被选中的数量
        foreach ($menuLists as $key => $data) {
            $res = $this->role->getMenuChildIsChecked($data->id, $id);
            if ($res) {
                $data->thisIsChecked = 1;
            } else {
                $data->thisIsChecked = 0;
            }
            $twoMenu = $menu->getTwoMenu($data->id, 2);
            foreach ($twoMenu as $key => $value) { //获取菜单下操作
                $menuAction = $menu->getMenuAction($value->id);
                //获取当前角色的当前菜单下选中操作列表（数组）
                $checkedArr = $role->getRoleMenuActionArr($value->id, $id);
                foreach ($menuAction as $key => $chk) {
                    //判断当前操作是否已授权当前角色
                    if (is_array($checkedArr) && in_array($chk->id, $checkedArr)) {
                        //已授权
                        $chk->menu_action_checked = 1;
                    } else {
                        //未授权
                        $chk->menu_action_checked = 0;
                    }
                }
                $value->menu_action = $menuAction;
            }
            $data->twoMenu = $twoMenu;
            if (!count($data->twoMenu)) {
                $menuAction = $menu->getMenuAction($data->id);
                //获取当前角色的当前菜单下选中操作列表（数组）
                $checkedArr = $role->getRoleMenuActionArr($data->id, $id);
                foreach ($menuAction as $key => $chk) {
                    //判断当前操作是否已授权当前角色
                    if (is_array($checkedArr) && in_array($chk->id, $checkedArr)) {
                        //已授权
                        $chk->menu_action_checked = 1;
                    } else {
                        //未授权
                        $chk->menu_action_checked = 0;
                    }
                }
                $data->menu_action = $menuAction;
            }
        }
        //获取授权菜单ID
        $menuIdObj = $this->role->getRoleMenuIdArr($id);
        $menuIdArr[] = array();
        foreach ($menuIdObj as $data) {
            $menuIdArr[] = $data->menu_id;
        }
        return view("manage::role.auth")
            ->with("roleMenuIdArr", $menuIdArr)
            ->with("roles_id", $id)
            ->with("menuLists", $menuLists)
            ->with("thisAction", '/roles')
            ->with("title", "授权管理");
    }
}