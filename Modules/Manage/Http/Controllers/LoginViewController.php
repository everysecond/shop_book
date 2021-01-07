<?php

namespace Modules\Manage\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class LoginViewController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    
    
    public function login()
    {
        return view("manage::login");
    }
    
    public function unauthorized()
    {
        return view("manage::unauthorized")
            ->with("title", "未授权")
            ->with("thisAction", "/");
    }
}
