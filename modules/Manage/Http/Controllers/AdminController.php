<?php

namespace Modules\Manage\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Modules\Manage\Models\Admin;
use App\Classlib\FileUpload;
use App\Classlib\FormCheck;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->admin = new Admin();
        $this->formCheck = new FormCheck();
    }
    
    //添加用户
    public function store(Request $request)
    {
        $this->actionLog("添加管理员", $request->ip());
        $checkRealname = $this->formCheck->isEmpty($request->realname, "用户名称");
        if (!$checkRealname->code) {
            return result($checkRealname->msg);
        }
        $checkUsername = $this->formCheck->isEmpty($request->username, "用户账号");
        if (!$checkUsername->code) {
            return result($checkUsername->msg);
        }
        $checkPassword = $this->formCheck->isEmpty($request->password, "用户密码");
        if (!$checkPassword->code) {
            return result($checkPassword->msg);
        }
        /*
        $checkAvator = $this->formCheck->isEmpty($request->avator,"用户头像");
        if(!$checkAvator->code){
            return result($checkAvator->msg);
        }
        */
        $checkRemark = $this->formCheck->strLenthCheck($request->remark, "备注信息", 0, 30, 2);
        if (!$checkRemark->code) {
            return result($checkRemark->msg);
        }
        
        $result = $this->admin->add($request);
        return result($result->msg, $result->code);
    }
    
    //编辑用户
    public function update(Request $request)
    {
        $this->actionLog("修改用户信息");
        $formCheck = new FormCheck();
        $checkMenuId = $formCheck->isEmpty($request->id, "用户不存在");
        if (!$checkMenuId->code) {
            return result("用户不存在！");
        }
        $checkRealname = $this->formCheck->isEmpty($request->realname, "用户名称");
        if (!$checkRealname->code) {
            return result($checkRealname->msg);
        }
        if ($request->username) {
            //判断用户账号是否已经存在
            $checkUsernameIsExt = $this->admin->checkIsUsernameExts($request->username);
            if ($checkUsernameIsExt->code) {
                return result($checkUsernameIsExt->msg);
            }
        }
        
        $checkAvator = $this->formCheck->isEmpty($request->avator, "用户头像");
        if (!$checkAvator->code) {
            return result($checkAvator->msg);
        }
        
        $checkRemark = $this->formCheck->strLenthCheck($request->remark, "备注信息", 0, 30, 2);
        if (!$checkRemark->code) {
            return result($checkRemark->msg);
        }
        
        $result = $this->admin->adminUpdate($request);
        return result($result->msg, $result->code);
    }
    
    //更新用户状态
    public function adminStatus(Request $request)
    {
        $this->actionLog("更新管理员状态");
        $checkId = $this->formCheck->isEmpty(intval($request->id), "所选用户");
        if (!$checkId->code) {
            return result($checkId->msg);
        }
        $adminStatus = $request->is_show;
        if ($adminStatus != 2 && $adminStatus != 1) {
            return result("所选择状态不存在！");
        }
        $result = $this->admin->upAdminStatus(intval($request->id), intval($adminStatus));
        return result($result->msg, $result->code);
    }
    
    //删除管理员
    public function adminDelete(Request $request)
    {
        $this->actionLog("删除管理员");
        $formCheck = new FormCheck();
        $checkMenuId = $formCheck->isEmpty($request->id, "用户不存在");
        if (!$checkMenuId->code) {
            return result("用户不存在！" . $request->id);
        }
        $result = $this->admin->adminDelete($request->id);
        return result($result->msg, $result->code);
    }
    
    
    protected function guard()
    {
        return Auth::guard('guard-name');
    }
    
    public function setpass(Request $request)
    {
        $checkOldPass = $this->formCheck->isEmpty($request->oldpass, "原密码");
        if (!$checkOldPass->code) {
            return result($checkOldPass->msg);
        }
        $checkPassword = $this->formCheck->isEmpty($request->password, "新密码");
        if (!$checkPassword->code) {
            return result($checkPassword->msg);
        }
        
        $checkPassword = $this->formCheck->isEmpty($request->password, "新密码");
        if (!$checkPassword->code) {
            return result($checkPassword->msg);
        }
        $checkPasswordSame = $this->formCheck->passwordSame($request->password, $request->repassword);
        if (!$checkPasswordSame->code) {
            return result($checkPasswordSame->msg);
        }
        $adminInfo = Session::get("adminInfo");
//        p(Auth::user()->username);die;
        $checkPsss = $this->admin->checkPassword(Auth::user()->mobile, $request->oldpass, 1);
  
        if (!$checkPsss->code) {
            return result($checkPsss->msg);
        }
        
        
        $result = $this->admin->setPassword(Auth::user()->mobile, $request->password);
        if ($result->code) {
            //退出登录
            $this->auth()->logout();
            return result("密码修改成功，请重新登录！", $result->code);
        } else {
            return result($result->msg, $result->code);
        }
    }
    
    //头像上传
    public function uploadPortrait(Request $request)
    {
        //生成头像的裁剪尺寸
        $cutSize = array(
            '100x100' => array(
                "imageWidth" => 100,
                "imageHeight" => 100,
            ),
            '30x30' => array(
                "imageWidth" => 30,
                "imageHeight" => 30,
            )
        );
//        p($request->image);
//        die;
        $imgUpload = new FileUpload();
        $result = $imgUpload->imageUpload($request->image, 2, 1, $cutSize);
        
        return result($result->msg, $result->code, $result->data);
    }
}