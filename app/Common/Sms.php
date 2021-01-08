<?php
namespace App\Common;


use Cache;
use Log;
use DateTime;
//use App\Jobs\Queue;
use Illuminate\Support\Facades\Hash;
use App\Common\Exceptions\ApiException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Sms {

    protected static $sign = '【快点动力】';
    protected static $is_sign = false;
    protected static $target = "http://106.ihuyi.com/webservice/sms.php?method=Submit";
    protected static $account = 'cf_koodpower';
    protected static $apikey = '869c0f09b3f6348b787aadef8f2a0538';
    protected static $sign_type = 0;



    const SIGN_PAD_LEFT = 0;        //签名填充位置
    const SIGN_PAD_RIGHT = 1;
    const EXPIRE = 5*60;      //短信有效期 秒
    const INTERVAL = 60;    //短信发送价格 秒

    protected static $templates = [
        'login' => "您的验证码是：code。请不要把验证码泄露给其他人。",
    ];

    /**
     * 发送短信,验证之前有没有发送过 一般验证码使用
     * @param $mobile
     * @return mixed
     */
    public static function sendVerify($type, $mobile) {
        static::checkInterval($type, $mobile);
        $code = static::getRandCode();
        $content = static::getContent(static::$templates[$type], [
            'code' => $code
        ]);

        static::setCache($type, $mobile, $code);

        //发送短信
        $post_data = static::encryption($mobile,$content);
        $gets =  xml_to_array(Post($post_data, static::$target));

        return $gets;
    }

    public static function encryption($mobile,$content){
        $time = time();
        $target = static::$target;
        $account = static::$account;
        $apikey = static::$apikey;;
        $post_data = "account=".$account."&password=".$apikey."&mobile=".$mobile."&content=".rawurlencode($content);


        return $post_data;
    }




    public static function getContent($content, $data = []) {
        $content = strtr($content, $data);
        if (static::$is_sign) {
            if (static::$sign_type == static::SIGN_PAD_LEFT) {
                $content = static::$sign . $content;
            } else {
                $content .= static::$sign;
            }
        }
        return $content;
    }


    /**
     * 检查验证码有效性
     * @param $type
     * @param $mobile
     * @param $code
     * @param $pull
     * @return bool
     */
    public static function checkCode($type, $mobile, $code, $pull = true) {
        $key = static::getCacheKey($mobile, $type);
        $cache = Cache::get($key);
        if (!$cache) {
            return false;
        }
        if (Hash::check($code, $cache) === true) {
            if ($pull) {
                Cache::forget($key);
            }
            return true;
        }
        return false;
    }

    /**
     * 移除短信缓存
     * @param $type
     * @param $mobile
     */
    public static function removeCache($type, $mobile) {
        $key = static::getCacheKey($mobile, $type);
        return Cache::forget($key);
    }

    /**
     * 随机数字
     * @return int
     */
    public static function getRandCode() {
        return mt_rand(1000, 9999);
    }


    /**
     * 添加短信缓存
     * @param $mobile
     * @param $type
     */
    public static function setCache($type, $mobile, $code) {
        $key = static::getCacheKey($mobile, $type);
        $code = static::getHashCode($code);
        Log::info($key . '=>' . $code);
        return Cache::put($key, $code, static::EXPIRE);
    }

    /**
     * 短信缓存键名
     * @param $mobile
     * @param $type
     * @return string
     */
    public static function getCacheKey($mobile, $type) {
        return strtolower($type) . '_sms_' . $mobile;
    }


    /**
     * 短信验证码HASH
     * @param $code
     * @return string
     */
    public static function getHashCode($code) {
        return Hash::make($code);
    }

    /**
     * 检查短信发送间隔
     * @param $type
     * @param $mobile
     * @throws ApiException
     */
    public static function  checkInterval($type, $mobile) {
        $key = $type . 'smsinterval_' . $mobile;
        $next_time = Cache::get($key);


        if ($next_time && $next_time > time()) {
            $time_left = $next_time - time();   //剩余时间
            throw new BadRequestHttpException('请过' . $time_left . '秒后重试');
        } else {

            $next_time = time() + static::INTERVAL; //下次发送时间
            $datetime = new DateTime();
            $datetime->setTimestamp($next_time);
            Cache::put($key, $next_time, $datetime);
        }
    }
}