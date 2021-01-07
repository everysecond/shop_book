<?php

namespace Modules\Kood\Models;

use App\Common\Exceptions\AjaxException;
use Modules\Kood\Models\BaseModel;
use Route;
use App\Common\Helpers\Tree;

class Lookup extends BaseModel {
    public $timestamps;
    public $guarded = [];

    protected $scenarios = [
        'default' => ['pid', 'name', 'call', 'code', 'style', 'sort', 'remark'],
    ];

    public function validateRules($request) {
        return [
            'default' => [
                'name' => 'required|max:20',
                'call' => ''
            ],
        ];
    }


    public function validateMessage() {
        return [
            'default' => [
                'name.required' => '名称不能为空',
                'name.max' => '名称能小于:max位',
            ]
        ];
    }

    /**
     * 获取某一级
     * @param int $deep
     * @return array
     */
    public function scopeDeep($query, $deep = 1) {
        $result = $query->where('deep', $deep);
    }

    /**
     * 分组缓存
     * @var
     */
    protected static $_items = [];

    /**
     * 获取分组全部数据
     * @param String $name
     */
    public static function items($call, $params = []) {
        $params += ['key' => 'code', 'value' => 'name'];
        if (!isset(static::$_items[$call])) {
            self::loadItems($call);
        }
        return self::$_items[$call]->pluck($params['value'], $params['key'])->toArray();
    }

    /**
     * 获取分组单个数据
     * @param String $name
     */
    public static function item($call, $key, $params = []) {
        $params += ['key' => 'code', 'value' => 'name'];
        if (!isset(self::$_items[$call])) {
            self::loadItems($call);
        }
        $items = self::$_items[$call]->pluck($params['value'], $params['key']);
        return isset($items[$key]) ? $items[$key] : null;

    }

    /**
     * 从数据库获取某个分组
     * @param String $name
     */
    protected static function loadItems($call) {
        $id = self::deep()->where('call', $call)->value('id');
        self::$_items[$call] = self::where('pid', $id)->sort()->get();
    }

    /**
     * 没有remark时使用name
     * @param $value
     */
    public function getRemarkAttribute($value) {
        return $value ?: $this->name;
    }

    /**
     * 重写 按code排序  考虑把实例化分离
     * @param $query
     * @return Object
     */
    public function scopeTree($query) {
        $result = $query->orderBy('pid', 'ASC')->orderBy('sort', 'DESC')->orderBy('code', 'ASC')->orderBy('id', 'ASC')->get();
        return new Tree($result);
    }

    /**
     * 获取code
     * @param $call
     * @param null $key
     * @return null
     */
    public static function getCode($call, $key) {
        return self::item($call, $key, ['key' => 'call', 'value' => 'code']);
    }


    /**
     * 通过调用码获取状态码
     * @param $type string 分类名
     * @param $call sting 调用名
     * @return int  状态码
     */
    public static function getCodeByCall($type, $call) {
        return self::item($type, $call, ['key' => 'call', 'value' => 'code']) ?: 0;
    }

    /**
     * 通过状态码获取调用码
     * @param $type 分类名
     * @param $code 状态码
     * @return string
     */
    public static function getCallByCode($type, $code) {
        return self::item($type, $code, ['key' => 'code', 'value' => 'call']) ?: '';
    }

    /**
     * 通过状态码获取备注
     * @param $type 分类名
     * @param $code 状态码
     * @return string
     */
    public static function getRemarkByCode($type, $code) {
        return self::item($type, $code, ['key' => 'code', 'value' => 'remark']) ?: '';
    }

    /**
     * 通过状态调用码获取备注
     * @param $type
     * @param $call
     * @return string
     */
    public static function getRemarkByCall($type, $call) {
        return self::item($type, $call, ['key' => 'call', 'value' => 'remark']) ?: '';
    }

    /**
     * 通过状态码获取样式
     * @param $type 分类名
     * @param $code 状态码
     * @return null
     */
    public static function getStyleByCode($type, $code) {
        return self::item($type, $code, ['key' => 'code', 'value' => 'style']) ?: 'default';
    }


    /**
     * 通过状态码获取分类名
     * @param $type
     * @param $code
     * @return null
     */
    public static function getNameByCode($type, $code) {
        return self::item($type, $code, ['key' => 'code', 'value' => 'name']) ?: '未知';
    }
    /**
     * 通过调用码获取分类名
     * @param $type
     * @param $code
     * @return null
     */
    public static function getNameByCall($type, $call) {
        return self::item($type, $call, ['key' => 'call', 'value' => 'name']) ?: '未知';
    }
    /**
     * 直接返回带颜色的label标签
     * @param $type
     * @param $code
     * @param string $tag
     * @param array $params
     * @return \Illuminate\Support\HtmlString
     */
    public static function label($type, $code, $tag = 'span', $params = [], $popup = false) {
        if ($popup) {
            $params = array_merge($params, [
                'data-popup' => 'popover',
                'data-content' => static::getRemarkByCode($type, $code),
                'data-trigger'=>'hover',
                'data-delay'=>300
            ]);
        }
        return \Html::tag($tag, static::getNameByCode($type, $code), array_merge($params, [
            'class' => 'label label-' . static::getStyleByCode($type, $code),
            'style'=>'cursor: help',
        ]));
    }

}

