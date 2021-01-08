<?php

namespace App\Classlib;

use Illuminate\Support\Facades\Cache;

class FormCheck
{
    /**验证手机号码格式
     * @param $mobile
     * @return mixed
     */
    public function mobile($mobile)
    {
        if (!$this->isMobile($mobile)) {
            return $this->result("手机号码格式不正确！");
        } else {
            return $this->result("验证通过！", 1);
        }
    }
    
    /**验证邮箱格式
     * @param $mobile
     * @return mixed
     */
    public function email($email)
    {
        if (!$this->isEmail($email)) {
            return false;
        } else {
            return true;
        }
    }
    
    
    /**验证数据是否为空
     * @param $data
     * @param string $msg
     * @return mixed
     */
    public function isEmpty($data, $msg = "数据")
    {
        if ($data == "" || $data == null || $data == false) {
            return $this->result($msg . "不能为空!");
        } else {
            return $this->result("验证通过！", 1);
        }
    }
    
    /**
     * 验证金额格式是否合法
     * @param $data
     * @param string $msg
     * @return mixed
     */
    public function checkMoney($money, $msg = "数据")
    {
        //验证是否是整数
        if (preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $money)) {
            return $this->result($msg . "验证通过", 1);
        } else {
            return $this->result($msg . "格式不正确");
        }
    }
    
    /**
     * 验证百分比格式是否合法
     * @param $data
     * @param string $msg
     * @return mixed
     */
    public function checkPercent($percent, $msg = "数据")
    {
        if (preg_match('/^[0-9]+(.[0-9]{1,2})?$/', $percent)) {
            if ($percent > 100) {
                return $this->result($msg . "不能大于100%");
            } else {
                return $this->result($msg . "验证通过", 1);
            }
        } else {
            return $this->result($msg . "格式不正确");
        }
    }
    
    /**验证密码强度
     * @param $password
     * @param $strength 密码强度 1低,2中,3高
     * @return mixed
     */
    public function password($password, $strength = 1)
    {
        if ($strength == 1) {
            if (strlen($password) < 6 || strlen($password) > 18) {
                return $this->result("密码必须是6-18个字符");
            } else {
                return $this->result("验证通过！", 1);
            }
        } elseif ($strength == 2) {
        
        } elseif ($strength == 3) {
            $pattern = "/^(?=.*?[0-9])(?=.*?[A-Z])(?=.*?[a-z])[0-9A-Za-z!-)]{8,}$/";
            if (!preg_match($pattern, $password)) {
                return $this->result("长度不小于8个字符,至少含有一个数字,至少含有一个小写字母,至少含有一个大写字母,可以有特殊字符，也可以没有");
            } else {
                return $this->result("验证通过！", 1);
            }
        }
    }
    
    /**验证两次输入的密码是否相同
     * @param $password
     * @param $repassword
     * @return mixed
     */
    public function passwordSame($password, $repassword)
    {
        if (trim($password) === trim($repassword)) {
            return $this->result("验证通过！", 1);
        } else {
            return $this->result("两次输入的密码不一致！");
        }
    }
    
    /**验证手机验证码的正确性
     * @param $mobile
     * @param $code
     * @param $codeType 验证码类型：1注册，2密码找回，3密码修改,
     * @return mixed
     */
    public function checkSmsCode($mobile, $smscode, $codeType = 1)
    {
        $codeArr = Cache::get("mobileSmsCode");
        if (!$codeArr) {
            return $this->result("验证码不存在！");
        } elseif ($codeArr->time + 600 < time()) {  //验证码10分钟内有效
            Cache::forget("mobileSmsCode");  //清除验证码信息
            return $this->result("验证码已过期！");
        } elseif ($codeArr->type != $codeType) {
            return $this->result("验证码不存在！");
        } else {
            if ($mobile != $codeArr->mobile) {
                return $this->result("手机号码不匹配！");
            } elseif ($smscode != $codeArr->code) {
                return $this->result("验证码不正确！");
            } else {
                Cache::forget("mobileSmsCode");
                return $this->result("验证通过！", 1);
            }
        }
    }
    
    /**中文姓名合法性验证
     * @param $name
     * @return mixed
     */
    public function isChinaName($name)
    {
        if (preg_match('/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/', $name)) {
            $msg = "验证通过！";
            $code = 1;
        } else {
            $msg = "姓名必须2-4个汉字！";
            $code = 0;
        }
        return $this->result($msg, $code);
    }
    
    /**
     * 验证用户性别
     * @param $name
     * @return mixed
     */
    public function checkSex($sex)
    {
        $sexArr = array(1, 2); //1男，2女
        if (empty($sex)) {
            $msg = "性别不能为空！";
            return $this->result($msg);
        } elseif (!in_array($sex, $sexArr)) {
            $msg = "所选性别不合法！";
            return $this->result($msg);
        } else {
            return $this->result("验证通过", 1);
        }
        return $this->result($msg);
    }
    
    /**年龄验证
     * @param $age
     * @param $idcard
     * @return mixed
     */
    public function checkAge($age, $idcard)
    {
        $idCardAge = $this->idCardToGetAge($idcard);
        if ($age == $idCardAge) {
            $res['msg'] = "验证通过！";
            $res['status'] = 1;
        } else {
            $res['msg'] = "您输入的年龄与身份证不符！";
            $res['status'] = 0;
        }
        return $res;
    }
    
    /**验证字符串长度
     * @param $str
     * @param string $msg
     * @param int $min 0不限制最少字数
     * @param int $max
     * @param int $type 1为字符，2为汉字
     * @return mixed
     */
    public function strLenthCheck($str, $msg = "数据", $min = 1, $max = 255, $type = 1)
    {
        if ($type == 1) {
            if ($min == 0) {
                if (strlen($str) > $max) {
                    $msgstr = $msg . "长度不能大于" . $max . "个字符！";
                    $status = 0;
                } else {
                    $msgstr = "验证通过！";
                    $status = 1;
                }
            } else {
                if (strlen($str) < $min) {
                    $msgstr = $msg . "长度不能小于" . $min . "个字符！";
                    $status = 0;
                } elseif (strlen($str) > $max) {
                    $msgstr = $msg . "长度不能大于" . $max . "个字符！";
                    $status = 0;
                } else {
                    $msgstr = "验证通过！";
                    $status = 1;
                }
            }
            return $this->result($msgstr, $status);
        } elseif ($type == 2) {
            if ($min == 0) {
                if (mb_strlen($str, "utf8") > $max) {
                    $msgstr = $msg . "长度不能大于" . $max . "个字！";
                    $status = 0;
                } else {
                    $msgstr = "验证通过！";
                    $status = 1;
                }
            } else {
                if (mb_strlen($str, "utf8") < $min) {
                    $msgstr = $msg . "长度不能小于" . $min . "个字！";
                    $status = 0;
                } elseif (mb_strlen($str, "utf8") > $max) {
                    $msgstr = $msg . "长度不能大于" . $max . "个字！";
                    $status = 0;
                } else {
                    $msgstr = "验证通过！";
                    $status = 1;
                }
            }
            return $this->result($msgstr, $status);
        }
    }
    
    /**
     * 检测图片是否是base64格式
     * @param $base64 图片
     * @param null $savePath 存储目录。（如“icons/”）如果不为空，则将图片存储到本地，并返回数据的data中包含image_url字段（存储路径），否则包含image、image_url字段（图片、图片存储路径）
     * @return mixed
     */
    public function base64Image($base64, $savePath, $saveImmediate = false)
    {
        $base64_image = str_replace(' ', '+', $base64);
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result)) {
            //匹配成功
            if ($result[2] == 'jpeg') {
                $image_name = time() . uniqid() . '.jpg';
                //纯粹是看jpeg不爽才替换的
            } else {
                $image_name = time() . uniqid() . '.' . $result[2];
            }
            $image = base64_decode(str_replace($result[1], '', $base64_image));
            $image_file = $savePath . $image_name;
            if ($saveImmediate) {
                //服务器文件存储路径
                try {
                    $imgPath = Storage::disk('local')->put($image_file, $image);
                } catch (\Exception $e) {
                    return result('图片存储出现异常:' . $e->getMessage());
                }
                $data['image_url'] = $image_file;
            } else {
                $data['image'] = $image;
                $data['image_url'] = $image_file;
            }
            return result('格式正确', 1, $data);
            // print_r($imagepaths);
        } else {
            return result('图片格式不正确');
        }
    }
    
    /**身份证号合法性验证
     * @param $idcard
     * @return mixed
     */
    public function isIdCard($idcard)
    {
        if ($this->checkIsIdCard($idcard)) {
            $msg = "验证通过！";
            $status = 1;
        } else {
            $msg = "身份证号码不正确请核对！";
            $status = 0;
        }
        return $this->result($msg, $status);
    }
    
    //通过身份证号获取年龄
    public function idCardToGetAge($idcard)
    {
        //过了这年的生日才算多了1周岁
        if (empty($idcard)) {
            return '';
        }
        $date = strtotime(substr($idcard, 6, 8));
        //获得出生年月日的时间戳
        $today = strtotime('today');
        //获得今日的时间戳
        $diff = floor(($today - $date) / 86400 / 365);
        //得到两个日期相差的大体年数
        
        //strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
        $age = strtotime(substr($idcard, 6, 8) . ' +' . $diff . 'years') > $today ? ($diff + 1) : $diff;
        return $age;
    }
    
    //验证身份证号是否合法
    protected function checkIsIdCard($id_card)
    {
        if (strlen($id_card) == 18) {
            return $this->idcard_checksum18($id_card);
        } elseif ((strlen($id_card) == 15)) {
            $id_card = $this->idcard_15to18($id_card);
            return $this->idcard_checksum18($id_card);
        } else {
            return false;
        }
    }
    
    // 计算身份证校验码，根据国家标准GB 11643-1999
    protected function idcard_verify_number($idcard_base)
    {
        if (strlen($idcard_base) != 17) {
            return false;
        }
        //加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        //校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($idcard_base); $i++) {
            $checksum += substr($idcard_base, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }
    
    // 将15位身份证升级到18位
    protected function idcard_15to18($idcard)
    {
        if (strlen($idcard) != 15) {
            return false;
        } else {
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
                $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
            } else {
                $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
            }
        }
        $idcard = $idcard . $this->idcard_verify_number($idcard);
        return $idcard;
    }
    
    // 18位身份证校验码有效性检查
    protected function idcard_checksum18($idcard)
    {
        if (strlen($idcard) != 18) {
            return false;
        }
        $idcard_base = substr($idcard, 0, 17);
        if ($this->idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
            return false;
        } else {
            return true;
        }
    }
    
    //手机号码验证
    protected function isMobile($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,3,6,7,8]{1}\d{8}$|^18[\d]{9}$#',
            $mobile) ? true : false;
    }
    
    //手机号码验证
    protected function isEmail($email)
    {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (preg_match($pattern, $email)) {
            return true;
        } else {
            return false;
        }
    }
    
    //数据返回
    protected function result($msg, $code = 0, $data = array())
    {
        $data['msg'] = $msg;
        $data['code'] = $code;
        $data['data'] = $data;
        return (object)$data;
//        return $this->arrayToObject($data);
    }
    
    /*
 * 数组 转 对象
 * @param array $arr 数组
 * @return object
 */
    public function arrayToObject($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array') {
                $arr[$k] = (object)arrayToObject($v);
            }
        }
        return (object)$arr;
    }
}