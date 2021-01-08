<?php

namespace Modules\Manage\Models;

use App\Models\Permission\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Admin extends Model
{
    protected $table = "admins";
    protected $table_role = "admin_roles";
    protected $table_managers = "managers";
    public $timestamps = false;
    
    public function getAdminLists()
    {
        $query = DB::table($this->table);
        $query = $query->leftjoin($this->table_role, $this->table . ".role_id", "=", $this->table_role . ".id");
        return $query->select("*", $this->table . ".id as admin_id", $this->table . ".create_date as admin_create_date",
            $this->table . ".update_date as admin_update_date",
            $this->table . ".id as admin_id")->orderBy($this->table . ".create_date", "ASC")->get();
    }
    
    public function checkIsAdmin($roleId)
    {
        $adminNum = self::where("role_id", "=", $roleId)->count();
        if (!$adminNum) {
            return returnData("无用户", 1);
        } else {
            return returnData("有用户");
        }
    }
    
    /**
     * 验证用户名密码是否正确
     * @para 登录账号
     * @para 登录密码
     * @para 是登录操作还是验证操作 true 为登录操作
     */
    public function checkPassword($username, $password, $isLogin = "")
    {
        
        $adminInfo = DB::table($this->table_managers)->where("mobile", "=", $username)->first();
     
        if ($adminInfo) {
            if ($adminInfo->status == 2) {
                return returnData("您的账号已被冻结！");
            } elseif (Hash::check($password, $adminInfo->password)) {
                if (!$isLogin) {
                    //获取用户组名称
                    $role = new Role();
                    $roleName = $role->getRoleName($adminInfo->role_id);
                    $adminInfo->role_name = $roleName;
                    $this->writeAdminInfo($adminInfo);
                    return returnData("登录成功！", 1);
                } else {
                    return returnData("验证成功", 1);
                }
            } else {
                return returnData("密码不正确！");
            }
        } else {
            return returnData("账号不存在！");
        }
    }
    
    //验证用户是否存在
    public function checkIsUsernameExts($username)
    {
        $res = self::where("username", "=", $username)->count();
        if (!$res) {
            return returnData("用户不存在!", 1);
        } else {
            return returnData("用户已存在！");
        }
    }
    
    public function writeAdminInfo($adminInfo)
    {
        Cookie::queue('adminInfo', $adminInfo, 24 * 60 * 7);
        Session::put("adminInfo", $adminInfo);
        return true;
    }
    
    //添加用户
    public function add($request)
    {
        $addData = array(
            "role_id" => intval($request->role_id),
            "realname" => htmlspecialchars($request->realname),
            "username" => htmlspecialchars($request->username),
            "sex" => intval($request->sex),
            "avator" => intval($request->avator),
            "password" => bcrypt(trim($request->password)),
            "state" => intval($request->state),
            "remark" => htmlspecialchars($request->remark),
            "create_date" => date("Y-m-d H:i:s"),
            "update_date" => date("Y-m-d H:i:s"),
        );
        self::insertGetId($addData);
        //更新排序
        return returnData("添加成功！", 1);
    }
    
    //更新用户状态
    public function upAdminStatus($adminId, $status)
    {
        $res = self::where("id", "=", $adminId)->update(['state' => $status]);
        if (!$res) {
            return returnData("更新失败！");
        } else {
            return returnData("更新成功！", 1);
        }
    }
    
    //编辑会员
    public function adminUpdate($request)
    {
        $updateData = array(
            "realname" => htmlspecialchars($request->realname),
            "username" => htmlspecialchars($request->username),
            "sex" => intval($request->sex),
            "avator" => htmlspecialchars($request->avator),
            "state" => intval($request->state),
            "remark" => htmlspecialchars($request->remark),
            "update_date" => date("Y-m-d H:i:s"),
        );
        if (!empty($request->password)) {
            $updateData['password'] = bcrypt(trim($request->password));
        }
        if (!empty($request->role_id)) {
            $updateData['role_id'] = $request->role_id;
        }
        self::where("id", "=", $request->id)->update($updateData);
        //更新排序
        return returnData("更新成功！", 1);
    }
    
    //删除菜单
    public function adminDelete($menuId)
    {
        $res = self::where("id", "=", $menuId)->delete();
        if (!$res) {
            return returnData("删除失败！", 0);
        } else {
            return returnData("删除成功！", 1);
        }
    }
    
    //修改密码
    public function setPassword($username, $password)
    {
        
        $res = DB::table($this->table_managers)->where("mobile", "=", $username)->update(["password" => bcrypt($password)]);
        if (!$res) {
            return returnData("修改失败！", 0);
        } else {
            return returnData("修改成功！", 1);
        }
    }
    
    //获取用户详情
    public function getAdminInfo($id)
    {
        return self::where("id", "=", $id)->first();
    }
    
    
    //获取已授权区域id列表
    public static function getAuthRegionIdList()
    {
        $role = new Role();
        $roleRegionArr = $role->getRoleRegionIdArr(Session::get("adminInfo")->role_id);
        $region_ids = array();
        foreach ($roleRegionArr as $data) {
            $region_ids[] = $data->region_id;
        }
        return $region_ids;
    }
}
