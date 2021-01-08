<?php
namespace App\Common\Helpers;

use Module;
use Breadcrumbs;
use Illuminate\Support\HtmlString;

class Helper {

    public static function parseHtmlTags($expression, $root = '') {
        if (stripos($expression, 'http://') === false && stripos($expression, 'https://') === false && stripos($expression, '//') === false) {
            $expression = preg_replace("/^(\\(')(.+?)('\\))$/", "('{$root}\\2')", $expression);
        }
        return $expression;
    }


    public static function breadcrumbs($menus, $parent = 'home') {
        foreach ($menus as $menu) {
            Breadcrumbs::register($menu['url'], function ($breadcrumbs) use ($parent, $menu) {
                $breadcrumbs->parent($parent);
                $breadcrumbs->push($menu['name'], $menu['real_url']);
            });
            if (isset($menu['children']) && 0 < count($menu['children'])) {
                static::breadcrumbs($menu['children'], $menu['url']);
            }
        }
    }


    /**
     * 获取所有系统模块
     * @param bool $strupper 是否转化为小写
     * @return array
     */
    public static function getAllModule($strupper = true) {
        $all_modules = array_keys(Module::all());
        $result = [];
        array_map(function ($val) use ($strupper, &$result) {
            if ($strupper) {
                $val = strtolower($val);
            }
            $result[$val] = $val;
        }, $all_modules);

        return $result;

    }

    /**
     * 加密解密
     * @param $string
     * @param string $operation
     * @param string $key
     * @param int $expiry 有效期 秒
     * @return string
     */
    public static function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;

        $key = md5($key ? $key : env('APP_KEY'));
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }

    }


    /**
     * 是否为电话号码
     * @param $phone
     * @return bool
     */
    public static function isPhone($phone) {
        return !!preg_match('/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$|(^1[3|4|5|7|8]\d{9}$)|(^[1|9]\d{4}$)|(^400-?\d{3}-?\d{4})/', $phone);
    }

    /**
     * 是否为手机号码
     * @param $string
     * @return bool
     */
    public static function isMobile($string) {
        return !!preg_match('/^1[3|4|5|6|7|8|9]\d{9}$/', $string);
    }

    /**
     * 是否为Email地址
     * @param $string
     * @return bool
     */
    public static function isEmail($string) {
        return !!preg_match('/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/i', $string);
    }


    /**
     * 格式化时间戳
     * @param $time
     * @param bool $stime
     * @return bool|string
     */
    public static function format_time($time, $stime = true) {
        if (!$time) {
            return '';
        }
        $format = 'Y-m-d' . ($stime ? ' H:i:s' : '');
        return date($format, $time);
    }


    /**
     * 生成缩略图图片地址
     * @param $url
     * @param int $width
     * @param int $height
     * @return mixed
     */
    public static function image($url, $width = 200, $height = 200) {
        if (strpos($url, 'http') === 0) {
            return $url;
        }
        return preg_replace_callback('/\.(jpg|jpeg|gif|png)$/i', function ($match) use ($width, $height) {
            return "!{$width}x{$height}" . $match[0];
        }, $url);
    }


    /**
     * 检查身份证合法性
     * @param $id_card
     * @return bool
     */
    public static function isIdcard($id_card) {
        if (strlen($id_card) == 18) {
            return static::idcard_checksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = static::idcard_15to18($id_card);
            return static::idcard_checksum18($id_card);
        } else {
            return false;
        }
    }

    public static function idcard_verify_number($idcard_base) {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);

        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    public static function idcard_15to18($idcard) {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . static::idcard_verify_number($idcard);
        return $idcard;
    }

    public static function idcard_checksum18($idcard) {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if (static::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * 验证价格
     * @param $data
     * @return mixed
     */
    public static function isPrice($data) {
        return preg_match('/^[0-9]+(\.[0-9]{0,2})?$/', $data) && $data > 0;
    }

    /**
     * 创建状态标签
     * @param $status
     * @return HtmlString
     */
    public static function status( $status ) {

        switch ($status) {
            case '0':
                $html = '<span class="label label-danger">关闭</span>';
                break;
            case '1':
                $html = '<span class="label label-primary">正常</span>';
                break;
            default :
                $html = '<span class="label label-default">未知</span>';
        }

        return new HtmlString($html);
    }

    /**
     * 创建上下架标签
     * @param $status
     * @return HtmlString
     */
    public static function shelving( $status ) {

        switch ($status) {
            case '0':
                $html = '<span class="label label-danger">下架</span>';
                break;
            case '1':
                $html = '<span class="label label-primary">上架</span>';
                break;
            default :
                $html = '<span class="label label-default">未知</span>';
        }

        return new HtmlString($html);
    }

    /**
     * 创建审核标签
     * @param $status
     * @return HtmlString
     */
    public static function check( $status ) {

        switch ($status) {
            case '0':
                $html = '<span class="label label-danger">审核中</span>';
                break;
            case '1':
                $html = '<span class="label label-success">已通过</span>';
                break;
            case '-1':
                $html = '<span class="label label-warning">不通过</span>';
                break;
            default :
                $html = '<span class="label label-default">未知</span>';
        }

        return new HtmlString($html);
    }

}