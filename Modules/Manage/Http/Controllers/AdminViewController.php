<?php

namespace Modules\Manage\Http\Controllers;

use Modules\Manage\Models\Admin;
use App\Models\Permission\Role;
use App\Classlib\FormCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminViewController extends Controller
{
    public function __construct()
    {
        $this->admin = new Admin();
        $this->formCheck = new FormCheck();
    }

    public function index(Request $request)
    {
        $this->actionLog("查看管理员");
        $adminLists = $this->admin->getAdminLists();
        $menuInfo = getMenuFromPath($request->path());
        return view("manage::admins.index")
            ->with("adminLists", $adminLists)
            ->with("thisAction", $menuInfo->url)
            ->with("title", $menuInfo->title);
    }


    //添加用户视图
    public function add()
    {
        //获取角色列表
        $role = new Role();
        $roleLists = $role->getRoleLists();
        $menuInfo = getMenuFromPath(env("BACKSTAGE_PREFIX") . "/admins");
        $adminInfo = Session::get("adminInfo");
        return view("manage::admins.add")
            ->with("thisAction", $menuInfo->url)
            ->with("title", $menuInfo->title)
            ->with("formAction", "addForm")
            ->with("roleLists", $roleLists);
    }    //添加用户视图


    //编辑用户视图
    public function edit(Request $request, $id)
    {
        //获取角色列表
        $role = new Role();
        $roleLists = $role->getRoleLists();
        $adminInfo = $this->admin->getAdminInfo($id);
        $adminInfo->avator_url = imageShow($adminInfo->avator, '100x100', url('images/default.png'));
        return view("manage::admins.edit")
            ->with("thisAction", '/admins')
            ->with("formAction", "editForm")
            ->with("title", "用户管理")
            ->with("roleLists", $roleLists)
            ->with("adminInfo", $adminInfo);
    }


    public function profile()
    {
        $adminInfo = Session::get('adminInfo');
        //获取角色列表
        $role = new Role();
        $roleLists = $role->getRoleLists();
        $adminInfo = $this->admin->getAdminInfo($adminInfo->id);
        $adminInfo->avator_url = imageShow($adminInfo->avator, '100x100', url('images/default.png'));
        return view("manage::admins.profile")
            ->with("thisAction", '/admins')
            ->with("formAction", "editForm")
            ->with("title", "用户管理")
            ->with("roleLists", $roleLists)
            ->with("adminInfo", $adminInfo);
    }

    public function repass()
    {
        return view("manage::admins.repass")
            ->with('title', '密码修改');
    }

    public function bases()
    {
        return view("admin.base")
            ->with("thisAction", '/bases');
    }
}
