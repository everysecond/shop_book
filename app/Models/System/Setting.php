<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class Setting extends Model
{
    public $timestamps = false;

    public function getSettingLists()
    {
        $adminInfo = Session::get("adminInfo");
        if($adminInfo->role_id==1){
            return self::where("state", "=", 1)->orderBy("sort","ASC")->get();
        }else{
            return self::where("is_public",0)->where("state", "=", 1)->orderBy("sort","ASC")->get();
        }
    }

    public function getDefaultSettingLists()
    {
        return self::where("is_public",0)->where("state", "=", 1)->orderBy("sort","ASC")->get();
    }



    public function saveBase($request)
    {
        foreach ($request->all() as $key => $val) {
            if ($key !== "_token") {
                self::where("key", "=", $key)->update(["value" => $val]);
            }
        }
        return returnData("修改成功！", 1);
    }

    public function getWebLogo()
    {
        $webLogo = self::where("key", "=", "web_logo")->first();
        if(!$webLogo->value){
            return url("storage/users/default.png");
        }else{
            return url("storage/".$webLogo->value);
        }
    }
    public function getWebQrcode()
    {
        $webQrcode = self::where("key", "=", "web_qrcode")->first();
        if(!$webQrcode->value){
            return url("images/nopic.png");
        }else{
            return url("storage/".$webQrcode->value);
        }
    }

}