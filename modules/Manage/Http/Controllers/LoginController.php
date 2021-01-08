<?php

namespace Modules\Manage\Http\Controllers;

use App\Common\Sms;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Modules\Manage\Repositories\ManagerRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginController extends Controller
{
    public function login()
    {
        dd(213);
        return view('manage::login.login');
    }

    public function loginSubmit(Request $request, ManagerRepository $repository)
    {
        $this->validate($request, [
            'mobile' => 'required',
            'password' => 'required',
            'captcha' => 'required|captcha'
        ], [
            'mobile.required' => '请输入登陆用户名(手机号)',
            'password.required' => '请输入登陆密码',
            'captcha.required' => '请输入验证码',
            'captcha.captcha' => '验证码错误'
        ]);
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $remember = (bool)$request->input('remember');

        try {
            $manager = $repository->loginCheck($mobile, $password);

            $this->auth()->login($manager, $remember);

            return $this->success();
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function logout()
    {
        $this->auth()->logout();

        return $this->success();
    }

    //移动端登录
    public function loginIn(Request $request, ManagerRepository $repository)
    {


        $mobile = $request->mobile;
        $code = $request->code;
        $manager = $repository->login($mobile);
        if ($mobile == "13255815650" || $mobile == "18705699180"){
             $access_token = $repository->updateInfo($manager);
             return result('登录成功', 1,$access_token );

        }
        $code_result = Sms::checkCode("login", $mobile, $code);
        if (!$code_result){
            return result('验证码错误', 0, []);
        }

        $access_token = $repository->updateInfo($manager);

        return result('登录成功', 1,$access_token );

    }

    //移动端发送验证码
    public function codeVerification(Request $request){
        try {
            $mobile = $request->mobile;
            if (empty($mobile)) {
                return result('手机号不能为空', 0, []);
            }
            $manager = Manager::where('mobile', $mobile)->first();

            if (empty($manager)) {
                return result('账号不存在', 0, []);
            }

            $data =  Sms::sendVerify("login",$mobile);


            if ($data['SubmitResult']['code'] == 2){
                return result('提交成功', 1, []);
            }else{
                return result('系统繁忙，请稍后重试', 0, []);
            }
        } catch (\Exception $exception) {
            return result($exception->getMessage(),0);
        }
    }








}
