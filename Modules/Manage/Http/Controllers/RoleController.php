<?php

namespace Modules\Manage\Http\Controllers;

use Modules\Manage\Models\Admin;
use App\Models\Permission\Role;
use App\Classlib\FormCheck;
use Illuminate\Http\Request;


class RoleController extends Controller
{
    public function __construct()
    {
        $this->formCheck = new FormCheck();
        $this->role = new Role();
    }
    
    //添加角色
    public function store(Request $request)
    {
        $checkTitle = $this->formCheck->isEmpty($request->rolename, "角色名称");
        if (!$checkTitle->code) {
            return result($checkTitle->msg);
        }
        $result = $this->role->add($request);
        return result($result->msg, $result->code);
    }
    
    //编辑角色
    public function roleUpdate(Request $request)
    {
        $checkRoleId = $this->formCheck->isEmpty($request->id, "角色不存在");
        if (!$checkRoleId->code) {
            return result("角色不存在！");
        }
        $checkTitle = $this->formCheck->isEmpty($request->rolename, "角色名称");
        if (!$checkTitle->code) {
            return result($checkTitle->msg);
        }
        $result = $this->role->RoleUpdate($request);
        return result($result->msg, $result->code);
    }
    
    //删除角色
    public function roleDelete(Request $request)
    {
        $formCheck = new FormCheck();
        $checkMenuId = $formCheck->isEmpty($request->id, "角色不存在");
        if (!$checkMenuId->code) {
            return result("角色不存在！" . $request->id);
        }
        $admin = new Admin();
        //判断角色下是否有用户
        $checkIsAdmin = $admin->checkIsAdmin(intval($request->id));
        if (!$checkIsAdmin->code) {
            return result("请先移除此角色下用户，再进行删除！");
        }
        $result = $this->role->roleDelete($request->id);
        return result($result->msg, $result->code);
    }
    
    public function editRoleAuth(Request $request)
    {
        $checkRole = $this->formCheck->isEmpty($request->roles_id, "授权组");
        if (!$checkRole->code) {
            return result($checkRole->msg);
        }
        $result = $this->role->saveRoleAuth($request);
        return result($result->msg, $result->code);
    }
}