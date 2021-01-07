<?php
/****************************************
 * @copyRight
 * @auth
 * @time 2018-04-08
 * Function golbal
 * 全局函数库
 ***************************************/

//获取全国三十四个省份名数组
function getAllProvincesName()
{
    return [
        "北京",
        "上海",
        "天津",
        "重庆",
        "黑龙江",
        "辽宁",
        "吉林",
        "河北",
        "河南",
        "湖北",
        "湖南",
        "山东",
        "山西",
        "陕西",
        "安徽",
        "浙江",
        "江苏",
        "福建",
        "广东",
        "海南",
        "四川",
        "云南",
        "贵州",
        "青海",
        "甘肃",
        "江西",
        "台湾",
        "内蒙古",
        "宁夏",
        "新疆",
        "西藏",
        "广西",
        "香港",
        "澳门"
    ];
}

//省名改简称
function syncProvincesName($provinces)
{
    $format = [];
    $search = ["省", "市", "自治区", "壮族自治区", "回族自治区", "维吾尔自治区", "特别行政区"];
    foreach ($provinces as $id => $province) {
        $str = str_replace($search, "", $province);
        $format[$id] = $str;
    }
    return $format;
}

//记录系统任务日志
function systemLog($content)
{
    $addData = array(
        "name"        => "system",
        "roles"       => "system",
        "content"     => $content,
        "create_date" => date("Y-m-d H:i:s")
    );
    \Modules\Manage\Models\Log::insertGetId($addData);
}

//当前登录人
function user()
{
    return Session::get('adminInfo');
}

//当前登录人Id
function userId()
{
    return user()->id;
}

//获取系统当前毫秒级时间戳
function getMillisecond()
{
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
}

/**
 * 计算指定日期前后N天
 * @param $date
 * @param int $days
 * @return false|string
 */
function getAppointDate($date, int $days = 0)
{
    return date("Y-m-d 00:00:00", strtotime($date) + ($days * 86400));
}

/**
 * 系统错误统一方法
 * @throws \App\Exceptions\SystemException
 */
function serverError($msg = 'internal_server_error')
{
    throw new \App\Exceptions\SystemException($msg,
        \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR);
}

/**
 * 404资源不存在
 */
function errNotFound()
{
    throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
}

//获取租点区域省份数组
function allLeaseProvinces()
{
    return \Modules\Lease\Models\BlAgent::allProvinces();
}

//获取当前登录用户可查看省份数组
function allUserProvinces($nationField = 0)
{
    $all = allLeaseProvinces();
    $all[$nationField] = "全部区域";
    ksort($all);
    $manager = Auth::user();
    if (!$manager->isSuper() && !$manager->isGlobal()) {
        if ($mobile = $manager->mobile) {
            $passportUser = \Modules\Lease\Models\PassportUser::query()->where("mobile", $mobile)->first();
            if ($passportUser && ($agents = $passportUser->agents())) {
                $provinceIds = $agents->pluck("agent_id")->toArray();
                $returnProvinces = [];
                foreach ($provinceIds as $id) {
                    if (isset($all[$id])) {
                        $returnProvinces[$id] = $all[$id];
                    }
                }
                if (count($returnProvinces)) {
                    return $returnProvinces;
                }
            }
        }
    }
    return $all;
}

//认领分配权限
function cdtRigths($cus_id)
{

    $sea_type = \Modules\Manage\Models\Crm\CrmUser::where('id', $cus_id)->pluck('sea_type')->toArray();


    $rights = \Modules\Manage\Models\Crm\CrmSeaStaff::where('sea_id', $sea_type[0])->where('staff_id', getUserId())->first();

    return $rights;
}


//获取区域所在省份
function getProvinceId($agentId)
{
    //缓存
    $cacheKey = 'report_agent_province_' . $agentId;

    if (Cache::has($cacheKey)) {
        return Cache::get($cacheKey);
    } else {
        $agent = getTopAgent($agentId);
        Cache::put($cacheKey, $agent, config('cache.cache_expired', 3600 * 8));
        return $agent;
    }
}

//获取manager表ID
function getManageId($user_id)
{
//    Cache::flush();
    //缓存
    $cacheKey = 'report_manage_user_' . $user_id;
    if (Cache::has($cacheKey)) {

        return Cache::get($cacheKey);
    } else {
        $passportUser = \Modules\Lease\Models\BlBusiness::query()->where("id", $user_id)->first();

        if (empty($passportUser)) {
            return [];
        }
        $manager_id = 0;
        if (!empty($passportUser->mobile)) {

            $manager = \App\Models\Manager::query()->where("mobile", $passportUser->mobile)->first();

            if ($manager) $manager_id = $manager->id;

        }

        $agent = ['id' => $passportUser->mobile, 'name' => $passportUser->nickname, 'manager_id' => $manager_id];
        Cache::put($cacheKey, $agent, config('cache.cache_expired', 3500));
        return $agent;
    }
}


function getTopAgent($id, int $lev = 0)
{
    $model = \Modules\Lease\Models\BlAgent::query()->where('id', (int)$id)->first();
    if (is_null($model)) {
        return [];
    }

    if ($model->pid != 0) {
        $lev += 1;
        return getTopAgent($model->pid, $lev);
    } else {
        return ['id' => $model->id, 'name' => $model->name, 'lev' => $lev];
    }
}

//php多个数组同键名键值相加合并
function comm_sumarrs($arr)
{
    $item = array();
    foreach ($arr as $key => $value) {

        foreach ($value as $k => $v) {
            if (isset($item[$k])) {
                $item[$k] = $item[$k] + $v;
            } else {
                $item[$k] = $v;
            }
        }
    }
    arsort($item);
    return $item;
}


/*
 *累计每小时 0-1  1-2 2-3 遍历输出
 * @param $msg
 * @param int $code
 * @param string $data
 * @return object
 */
function timehourarr()
{
    $i = 0;
    for ($x = 0; $x < 24; $x++) {
        $i++;
        $res[$i] = $x . '-' . $i;
    }

    return $res;
}

/*
 *累计每小时 0-1  0-2 0-3 遍历输出
 * @param $msg
 * @param int $code
 * @param string $data
 * @return object
 */
function timehoursarr()
{
    $i = 0;
    $j = 0;
    for ($x = 0; $x < 24; $x++) {
        $i++;
        $res[$i] = $j . '-' . $i;
    }

    return $res;
}

/*
 * 自定义格式化打印函数
 * @param string、object、array
 * @return 格式化后台数据
 */
function p($str)
{
    echo "<pre>";
    print_r($str);
    echo "</pre>";
}


//php多个数组同键名键值相加合并
function comm_sumarrss($arr)
{
    $item = array();
    foreach ($arr as $key => $value) {

        foreach ($value as $k => $v) {
            if (isset($item[$k])) {
                $item[$k] = $item[$k] + $v;
            } else {
                $item[$k] = $v;
            }
        }
    }
    ksort($item);
    return $item;
}

/*
 *格式化图片真实路径，并检测图片是否存在，若不存在自动输出默认图片
 * @param 图片路径
 * $param string 图片尺寸
 * @param string url 默认图片地址
 */
function imageShow($imageUrl, $imageSize = false, $defaultUrl = "")
{
    if ($imageSize) {
        //处理图片地址
        $imgResArr = explode('.', $imageUrl);
        $imgExt = $imgResArr[count($imgResArr) - 1];
        $imgSrc = url($imageUrl . $imageSize . '.' . $imgExt);
        if ($defaultUrl) {
            return $imgSrc;
            //return $imgSrc && fileExists($imgSrc) ? $imgSrc : $defaultUrl;
        } else {
            //return  $imgSrc && fileExists($imgSrc) ? $imgSrc : url("images/nopic.png");
            return $imgSrc;
        }
    } else {
        if ($defaultUrl) {
            return $imageUrl && fileExists(url($imageUrl)) ? url($imageUrl) : $defaultUrl;
        } else {
            return $imageUrl && fileExists(url($imageUrl)) ? url($imageUrl) : url("images/nopic.png");
        }
    }
}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function getCity($ip = '')
{
    if ($ip !== '') {
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
        $ip = json_decode(file_get_contents($url), true);
        $data = $ip;
    } else {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
        $ip = json_decode(file_get_contents($url));
        if ((string)$ip->code == '1') {
            return false;
        }
        $data = $ip->data;
    }
    return $data;
}

/*
 * 价格拆分，返回数组，索引0为整数部分，1为小数点后两位部份
 * @param price
 */
function themePrice($price)
{
    if (!empty($price)) {
        return $price = explode(".", $price);
    }
}


/*
 * array uniqueRand( int $min, int $max, int $num )
 * 生成一定数量的不重复随机数
 * $min 和 $max: 指定随机数的范围
 * $num: 指定生成数量
 */
function unique_rand($min, $max, $num)
{
    //初始化变量为0
    $count = 0;
    //建一个新数组
    $return = array();
    while ($count < $num) {
        //在一定范围内随机生成一个数放入数组中
        $return[] = mt_rand($min, $max);
        //去除数组中的重复值用了“翻翻法”，就是用array_flip()把数组的key和value交换两次。这种做法比用 array_unique() 快得多。
        $return = array_flip(array_flip($return));
        //将数组的数量存入变量count中
        $count = count($return);
    }
    //为数组赋予新的键名
    shuffle($return);
    return $return;
}

/*
 * 数组 转 对象
 * @param array $arr 数组
 * @return object
 */
function arrayToObject($arr)
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

function test($a)
{

    return $a;
}

/*
 * 语言包调取函数
 * @param $module 模块
 * @param $key 关键值
 * @return string 返回语言
 */
function lang($module, $key, $namespace = 'Admin')
{
    $lang = env("APP_LANG");
    $langPath = app_path($namespace . '/' . $module . '/Languages/');
    $langCont = include($langPath . $lang . ".php");
    return $langCont[$key];
}

/*
 * 对象 转 数组
 * @param array $arr 数组
 * @return object
 */
function objectToArray($array)
{
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = objectToArray($value);
        }
    }
    return $array;
}

/*
 * 数组 转 XML
 * @param array $arr 数组
 * @return XML
 */
function arrayToXml($arr)
{
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<PACKET TYPE="RESPONSE"><BODY><PAY_RESULT>';
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        }
    }
    $xml .= "</PAY_RESULT></BODY></PACKET> ";
    return $xml;
}

/*
 * 获取远程图片
 * @param url 远程图片地址
 * @param saveDir   保存路径，不输入默认保存到跟目录
 * @param filenmae  文件名称
 * param type  获取文件方式
 * @return array
 */
function getImage($url, $saveDir = '', $filename = '', $type = '')
{
    if (trim($url) == '') {
        return array('file_name' => '', 'save_path' => '', 'error' => 1);
    }
    if (trim($saveDir) == '') {
        $save_dir = './';
    }
    if (trim($filename) == '') {//保存文件名
        $ext = strrchr($url, '.');
        if ($ext != '.gif' && $ext != '.jpg') {
            return array('file_name' => '', 'save_path' => '', 'error' => 3);
        }
        $filename = time() . $ext;
    }
    if (0 !== strrpos($save_dir, '/')) {
        $save_dir .= '/';
    }
    //创建保存目录
    if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return array('file_name' => '', 'save_path' => '', 'error' => 5);
    }
    //获取远程文件所采用的方法
    if ($type) {
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        //amazon https://forums.aws.amazon.com/message.jspa?messageID=196878
        curl_close($ch);
    } else {
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img);
    //文件大小
    $fp2 = @fopen($save_dir . $filename, 'w');
    fwrite($fp2, $img);
    fclose($fp2);
    unset($img, $url);
    return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
}

/*
 * 发起curl get方式请求
 * @param url 请注地址
 * @return array
 */
function curl_https_get($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);//这个是重点。
    $data = curl_exec($curl);
    curl_close($curl);
    return $data;
}

/*
 * 手机号码隐藏中间数据
 * @param $phone
 * @return string
 */
function hidtel($phone)
{
    $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i', $phone); //固定电话
    if ($IsWhat == 1) {
        return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i', '$1****$2', $phone);
    } else {
        return preg_replace('/(1[3578]{1}[0-9])[0-9]{4}([0-9]{4})/i', '$1****$2', $phone);
    }
}

/*
 * 隐藏银行卡号中间几位数据
 * @param $bankCard
 * @return string
 */
function hidBankCard($bankCard)
{
    return substr_replace($bankCard, "**********", 4, 10);
}


/*
 *处理控制器间返回结果
 * @param $msg
 * @param int $code
 * @param string $data
 * @return object
 */
function returnData($msg, $code = 0, $data = "")
{
    $res['msg'] = $msg;
    $res['code'] = $code;
    $res['data'] = $data;
    return arrayToObject($res);
}


/*
 * 处理返回数据
 * @param $msg
 * @param int $code
 * @param string $data
 * @return json
 */
function result($msg, $code = 0, $data = "", $count = -1)
{
    $res['msg'] = $msg;
    $res['code'] = $code;
    $res['data'] = $data;
    if ($count >= 0) {
        $res['count'] = $count;
    }
    return json_encode($res);
}

function adminResult($msg, $code = 0, $count = 0, $data = "")
{
    $res['msg'] = $msg;
    $res['code'] = $code;
    $res['count'] = $count;
    $res['data'] = $data;
    return json_encode($res);
}

/*
 * 判断是否是Y-m-d格式
 * @param $date
 * @return bool
 */
function isDate($date)
{
    if ($date == date('Y-m-d', strtotime($date))) {
        return true;
    } else {
        return false;
    }
}

/*
 * 判断是否是Y-m-d H:i:s格式
 * @param $date
 * @return bool
 */
function isDateTime($date)
{
    if ($date == date('Y-m-d H:i:s', strtotime($date))) {
        return true;
    } else {
        return false;
    }
}

/*
 * 获取当前时间
 * @param $date
 * @return date
 */
function get_current_datetime()
{
    return date("Y-m-d H:i:s", time());
}

/*
 * 生成静态资源读取地址
 * @param $resource
 * @return url
 */
function asseturl($resource)
{
    return url("/resource/" . $resource);
}

/*
 * 格式化后台完整地址
 * @param $resource
 * @return url
 */
function adminurl($resource = "")
{
    return url(env("BACKSTAGE_PREFIX") . $resource);
}

/*
 * 获取后台菜单列表
 * @param $thisAction 当前操作
 * @return Html
 */
function getAdminMenuList($thisAction = "")
{
    $menu = new \Modules\Manage\Models\Menu();
    $role = new \App\Models\Permission\Role();
//    $adminInfo = \Illuminate\Support\Facades\Session::get("adminInfo");
    $menuAuthObj = $role->getRoleMenuIdArr(1);
    $menuIdArr = array();
    foreach ($menuAuthObj as $data) {
        $menuIdArr[] = $data->menu_id;
    }

    $list = $menu->getFirstMenu(2);
    $str = '<ul class="layui-nav layui-nav-tree" lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">';
    foreach ($list as $data) {
        $twoMenuList = $menu->getTwoMenu($data->id, 2);
        if (!count($twoMenuList)) {
            //判断一级菜单是否有权限查看
            if (in_array($data->id, $menuIdArr)) { //判断是否有权限访问此菜单
                if ($data->url == $thisAction) {
                    $str .= '<li data-name="home" class="layui-nav-item layui-nav-itemed">';
                } else {
                    $str .= '<li data-name="component" class="layui-nav-item">';
                }
                $str .= '<a class="loadHref" href="' . adminurl($data->url) . '"><i class="iconfont">' . htmlspecialchars_decode($data->icon_class) . '</i><cite> ' . $data->title . '</cite></a>';
                $str .= '</li>';
            }
        } else {
            $strChild = "";
            $thisChild = false;
            foreach ($twoMenuList as $value) {
                if (in_array($value->id, $menuIdArr)) { //判断是否有权限访问此菜单
                    if ($value->url == $thisAction) {
                        $strChild .= '<dd class="layui-this" data-name="console" ><a lay-href="' . adminurl($value->url) . '"><i class="iconfont">' . htmlspecialchars_decode($value->icon_class) . '</i> ' . $value->title . '</a></dd>';
                        $thisChild = true;
                    } else {
                        $strChild .= '<dd><a class="loadHref" lay-href="' . adminurl($value->url) . '"><i class="iconfont">' . htmlspecialchars_decode($value->icon_class) . '</i> ' . $value->title . '</a></dd>';
                    }
                }
            }
            if ($strChild != "") { //判断此二级菜单下是否有子菜单
                if ($thisChild) {
                    $str .= '<li class="layui-nav-item layui-nav-itemed"  data-name="component">';
                } else {
                    $str .= '<li class="layui-nav-item"  data-name="component">';
                }
                $str .= '<a  href="javascript:;"><i class="iconfont">' . htmlspecialchars_decode($data->icon_class) . '</i><cite> ' . $data->title . '</cite></a>';
                $str .= '<dl class="layui-nav-child">';
                $str .= $strChild;
                $str .= '</dl>';
                $str .= '</li>';
            }
        }
    }
    $str .= '</ul>';
    echo $str;
}

/*
 * 获取后台管理员头像
 * @param $adminId 管理员ID
 * @return url
 */
function getAdminAvator($adminId = false)
{
    if (!$adminId) {
        $adminInfo = \Illuminate\Support\Facades\Session::get("adminInfo");
    } else {
        $admin = new \App\Models\Admin();
        $adminInfo = $admin->getAdminInfo($adminId);
    }
    return imageShow($adminInfo->avator, '30x30');
}

/*
 * 获取后台管理员名称
 * @param $adminId 管理员ID
 * @return str name
 */
function getAdminName($adminId = false)
{
    if (!$adminId) {
        $adminInfo = \Illuminate\Support\Facades\Session::get("adminInfo");
    } else {
        $admin = new \App\Models\Admin();
        $adminInfo = $admin->getAdminInfo($adminId);
    }
    if ($adminInfo->realname) {
        return $adminInfo->realname;
    } else {
        return "匿名";
    }
}

/*
 * 获取后台面包绡导航
 * @param $thisAction 当前操作
 * @param $thisName 当前操作名称（标题）
 * @return url
 */
function adminNav($thisAction = "", $thisName = "")
{
    $navHtml = '<span class="layui-breadcrumb">';
    if (!$thisAction) {
        $navHtml .= '<a><i class="layui-icon">&#xe68e;</i>后台主页</a>';
    } else {
        $navHtml .= '<a><i class="layui-icon">&#xe68e;</i>后台主页</a>';
        //获取菜单
        $menu = new \Modules\Manage\Models\Menu();
        $menuInfo = $menu->getMenuInfo($thisAction, 2);
        if ($menuInfo->parent_id != 0) {
            //获取父级菜单
            $parentMenuInfo = $menu->getMenuInfo($menuInfo->parent_id);
            if (count($parentMenuInfo)) {
                $navHtml .= '<a><i class="layui-icon"></i>' . $parentMenuInfo->title . '</a>';
            }
        }
        $navHtml .= '<a><i class="layui-icon"></i>' . $menuInfo->title . '</a>';
        if ($thisName) {
            $navHtml .= '<a><i class="layui-icon"></i>' . $thisName . '</a>';
        }
    }
    $navHtml .= '</span>';
    echo $navHtml;
}

function getMenuFromPath($path)
{
    $menuPath = ltrim($path, env("BACKSTAGE_PREFIX"));//过滤链接中存在多个后台关键词
    $menu = new \Modules\Manage\Models\Menu();
    //处理多级菜单当前状态问题
    $menuPath = rtrim($menuPath, "/");
    $exp = explode('/', $menuPath);
    $menuInfo = $menu->where("url", "=", '/' . $exp[1])->first();
    if (!count($menuInfo)) {
        $menuInfo['url'] = "/";
        $menuInfo['title'] = "后台主页";
        return arrayToObject($menuInfo);
    } else {
        return $menuInfo;
    }
}

//获取后台配置信息
function adminSetting($key)
{
    $setting = new \App\Models\System\Setting();
    $settingInfo = $setting->where("key", "=", $key)->first();
    if (empty($settingInfo)) {
        return "";
    } else {
        return $settingInfo->value;
    }
}

//检测远程文件是否存在
function fileExists($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_NOBODY, 1); // 不下载
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if (curl_exec($ch) !== false) {
        return true;
    } else {
        return false;
    }
}

/*
 * php实现下载远程文件保存到本地
 **
 * $url 图片所在地址
 * $path 保存图片的路径
 * $filename 图片自定义命名
 * $type 使用什么方式下载
 * 0:curl方式,1:readfile方式,2file_get_contents方式
 *
 * return 文件名
 */
function getFile($url, $path = '', $filename = '', $type = 0)
{
    if ($url == '') {
        return false;
    }
    //获取远程文件数据
    if ($type === 0) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);//最长执行时间
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//最长等待时间

        $img = curl_exec($ch);
        curl_close($ch);
    }
    if ($type === 1) {
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
    }
    if ($type === 2) {
        $img = file_get_contents($url);
    }
    //判断下载的数据 是否为空 下载超时问题
    if (empty($img)) {
        throw new \Exception("下载错误,无法获取下载文件！");
    }
    //没有指定路径则默认当前路径
    if ($path === '') {
        $path = "./";
    }
    //如果命名为空
    if ($filename === "") {
        $filename = md5($img);
    }
    //获取后缀名
    $ext = substr($url, strrpos($url, '.'));
    if ($ext && strlen($ext) < 5) {
        $filename .= $ext;
    }
    //防止"/"没有添加
    $path = rtrim($path, "/") . "/";
    $fp2 = @fopen($path . $filename, 'a');
    fwrite($fp2, $img);
    fclose($fp2);
    return $filename;
}

/*
 * 复制目录
 * @param $src 原目录
 * @param 目标目录
 */
function copyDir($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
                continue;
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    return true;
}

//删除目录
function delDir($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir("$dir/$file") ? delDir("$dir/$file") : @unlink("$dir/$file");
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        @rmdir($dir);
    }
    return true;
}

//验证操作是否显示
function actionIsView($actionKey)
{
    $menu = new \Modules\Manage\Models\Menu();
    $res = $menu->getMenuActionIsAuth($actionKey);
    if ($res) {
        return true;
    } else {
        return false;
    }
}

//时间类型表单选择
function timeType()
{
    $data = array(
        '0' => array(
            'id'   => 1,
            'name' => '最近一周',
        ),
        '1' => array(
            'id'   => 2,
            'name' => '最近15天',
        ),
        '2' => array(
            'id'   => 3,
            'name' => '最近一个月',
        ),
        '3' => array(
            'id'   => 4,
            'name' => '最近三个月',
        ),
        '4' => array(
            'id'   => 5,
            'name' => '最近半年',
        ),
        '5' => array(
            'id'   => 6,
            'name' => '最近一年',
        ),
        '6' => array(
            'id'   => 7,
            'name' => '自定义时间段',
        ),

    );
    return $data;
}

//时间类型表单选择
function dateType()
{
    $data = array(
        '0' => array(
            'id'   => 6,
            'name' => '最近一周',
        ),
        '1' => array(
            'id'   => 14,
            'name' => '最近15天',
        ),
        '2' => array(
            'id'   => 29,
            'name' => '最近一个月',
        ),
        '3' => array(
            'id'   => 89,
            'name' => '最近三个月',
        ),
        '4' => array(
            'id'   => 179,
            'name' => '最近半年',
        ),
        '5' => array(
            'id'   => 364,
            'name' => '最近一年',
        ),
        '6' => array(
            'id'   => -1,
            'name' => '自定义时间段',
        ),

    );
    return $data;
}


//时间范围选择
function selectTimeRange($id)
{
    $time = time();
    $yesterday = date("Y-m-d 23:59:59", strtotime("-1 days"));
    $data['end_time'] = $yesterday;
    $start_time = "";
    switch ($id) {
        case 1:
            $start_time = date("Y-m-d 00:00:00", strtotime("-7 days"));
            break;
        case 2:
            $start_time = date("Y-m-d 00:00:00", strtotime("-15 days"));
            break;
        case 3:
            $start_time = date("Y-m-d 00:00:00", strtotime("-1 months"));
            break;
        case 4:
            $start_time = date("Y-m-d 00:00:00", strtotime("-3 months"));
            break;
        case 5:
            $start_time = date("Y-m-d 00:00:00", strtotime("-6 months"));
            break;
        case 6:
            $start_time = date("Y-m-d 00:00:00", strtotime("-1 years"));
            break;
        case 8:
            $start_time = date("Y-m-d 00:00:00", strtotime("-1 days"));
            break;
        case 7:
            return false;


    }

    $data['start_time'] = $start_time;
    return $data;
}

function selectTimeStrtotime($id)
{
    $time = time();


    switch ($id) {
        case 1:
            $data['end_time'] = strtotime(date("Y-m-d 23:59:59", strtotime("-1 days")));
            $data['start_time'] = strtotime(date("Y-m-d 00:00:00", strtotime("-1 days")));
            break;
        case 2:
            $data['end_time'] = strtotime(date("Y-m-d 23:59:59", strtotime("-2 days")));
            $data['start_time'] = strtotime(date("Y-m-d 00:00:00", strtotime("-4 days")));
            break;
        case 3:
            $data['end_time'] = strtotime(date("Y-m-d 23:59:59", strtotime("-5 days")));
            $data['start_time'] = strtotime(date("Y-m-d 00:00:00", strtotime("-8 days")));
            break;
        case 4:
            $data['end_time'] = strtotime(date("Y-m-d 23:59:59", strtotime("-9 days")));
            $data['start_time'] = strtotime(date("Y-m-d 00:00:00", strtotime("-11 days")));
            break;
        case 5:
            $data['end_time'] = strtotime(date("Y-m-d 23:59:59", strtotime("-2 days")));
            $data['start_time'] = strtotime(date("Y-m-d 00:00:00", strtotime("-11 days")));
            break;
        case 6:
            $data['end_time'] = strtotime(date("Y-m-d 23:59:59", strtotime("-12 days")));
            $data['start_time'] = strtotime(date("Y-m-d 00:00:00", strtotime("-31 days")));
            break;
        case 7:
            return false;


    }

    return $data;
}


function selectTimeDate($id, $date)
{

    $time = strtotime($date);
//    $b = strtotime('-3 days', $a);

    switch ($id) {
        case 1:
            $data['end_time'] = date("Y-m-d 23:59:59", $time);
            $data['start_time'] = date("Y-m-d 00:00:00", $time);
            break;
        case 2:
            $data['end_time'] = date("Y-m-d 23:59:59", strtotime("+3 days", $time));
            $data['start_time'] = date("Y-m-d 00:00:00", strtotime("+1 days", $time));
            break;
        case 3:
            $data['end_time'] = date("Y-m-d 23:59:59", strtotime("+7 days", $time));
            $data['start_time'] = date("Y-m-d 00:00:00", strtotime("+4 days", $time));
            break;
        case 4:
            $data['end_time'] = date("Y-m-d 23:59:59", strtotime("+10 days", $time));
            $data['start_time'] = date("Y-m-d 00:00:00", strtotime("+8 days", $time));
            break;
        case 5:
            $data['end_time'] = date("Y-m-d 23:59:59", strtotime("+10 days", $time));
            $data['start_time'] = date("Y-m-d 00:00:00", strtotime("+1 days", $time));
            break;
        case 6:
            $data['end_time'] = date("Y-m-d 23:59:59", strtotime("+30 days", $time));
            $data['start_time'] = date("Y-m-d 00:00:00", strtotime("+ 11 days", $time));
            break;
        case 8:
            $data['end_time'] = date("Y-m-d 23:59:59", strtotime("+30 days", $time));
            $data['start_time'] = date("Y-m-d 00:00:00", $time);
            break;
        case 7:
            return false;


    }

    return $data;
}

//获取指定日期段内每一天的日期
function getDateRange($startdate, $enddate, $format = 'Y-m-d')
{
    $stime = strtotime($startdate);
    $etime = strtotime($enddate);
    $datearr = [];
    while ($stime <= $etime) {
        $datearr[] = date($format, $stime);//得到dataarr的日期数组。
        $stime = $stime + 86400;
    }
    return $datearr;
}

//xml格式转数组
function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

//添加随机数
function random($length = 6 , $numeric = 0) {
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if($numeric) {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}
//CURL请求
function Post($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}


function ApiUserProvinces($nationField = 0,$manager)
{
    $all = allLeaseProvinces();
    $all[$nationField] = "全部区域";
    ksort($all);

    if (!$manager->isSuper() && !$manager->isGlobal()) {
        if ($mobile = $manager->mobile) {
            $passportUser = \Modules\Lease\Models\PassportUser::query()->where("mobile", $mobile)->first();
            if ($passportUser && ($agents = $passportUser->agents())) {
                $provinceIds = $agents->pluck("agent_id")->toArray();
                $returnProvinces = [];
                foreach ($provinceIds as $id) {
                    if (isset($all[$id])) {
                        $returnProvinces[$id] = $all[$id];
                    }
                }
                if (count($returnProvinces)) {
                    return $returnProvinces;
                }
            }
        }
    }
    return $all;
}

function iunserializer($value) {
    if (empty($value)) {
        return array();
    }
    if (!is_serialized($value)) {
        return $value;
    }
    if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
        $result = unserialize($value, array('allowed_classes' => false));
    } else {
        if (preg_match('/[oc]:[^:]*\d+:/i', $value)) {
            return array();
        }
        $result = unserialize($value);
    }
    if (false === $result) {
        $temp = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($matchs) {
            return 's:' . strlen($matchs[2]) . ':"' . $matchs[2] . '";';
        }, $value);

        return unserialize($temp);
    } else {
        return $result;
    }
}

