<?php

namespace App\Common\Helpers;

class Tree {


    private $_data;

    public function __construct($data) {
        $this->_data = null;
        $this->_data = $data;
    }

    /**
     * 无限级分类
     *
     * @access public
     * @param $models
     * @param null $id
     * @param bool $children
     * @return Array $treeList
     * @internal param Array $data
     * @internal param Int $sid
     * @internal param Int $count
     */
    public static function init($models, $id = null, $children = false) {
        $data = [];
        foreach ($models as $key => $model) {
            if ($id == $model->id) continue;
            $data[$model->pid][$model->id] = $model;
        }
        if (!$id || !$children) $id = 0;
        return collect(static::_init($data, $id));
    }

    public static function _init($data, $pid = 0, $level = 1) {
        $list = [];
        if (isset($data[$pid])) {
            $j = 0;
            $c = count($data[$pid]);
            foreach ($data[$pid] as $key => $value) {
                $j++;
                $value->level = $level;
                if (1 != $level) {
                    $nbsp = '';
                    for ($i = 0; $i < ($level - 2) * 4; $i++) {
                        $nbsp .= '&nbsp;&nbsp;';
                    }
                    if ($c == $j) {
                        $value->spacer = $nbsp . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─';
                    } else {
                        $value->spacer = $nbsp . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├─';
                    }

                } else {
                    $value->spacer = '';
                }
                $list[] = $value;
                if (isset($data[$value['id']])) {
                    $return = static::_init($data, $value['id'], $level + 1);
                    $list = array_merge($list, $return);
                }
            }
        }
        return $list;
    }

    /**
     * 转换成子树 转换前不用经过init过滤 index 索引
     *
     * @param  $data
     * @param string $name
     * @param int $sid
     * @return array
     */
    public static function toChildren($data, $name = 'children', $sid = 0) {
        $list = array();
        foreach ($data as $key => $value) {
            if ($sid == $value['pid']) {
                $list[] = $value;
                unset($data[$key]);
                $list[count($list) - 1][$name] = self::toChildren($data, $name, $value['id']);
            }
        }
        return $list;
    }


    /**
     * 格式化无限极分类,子分类添加占位符
     *
     * @param string $key
     * @param string $value
     * @param bool $spacer
     * @return array
     * @internal param array $params
     */
    public static function formatTree($data, $key = 'id', $value = 'name', $spacer = true) {
        $result = [];
        foreach ($data as $val) {
            $result[$val[$key]] = ($spacer ? $val['spacer'] : '') . $val[$value];
        }
        return collect($result);
    }

    /**
     * 获取 列表页格式
     */
    public function getList($id = null, $children = false) {
        return static::init($this->_data, $id, $children);
    }

    /**
     * 获取 select 下拉列表格式
     * @param $params
     * @return Array|array
     */
    public function getSelect($id = null, $params = array()) {
        $params['key'] = isset($params['key']) ? $params['key'] : 'id';
        $params['value'] = isset($params['value']) ? $params['value'] : 'name';
        $params['spacer'] = isset($params['spacer']) ? $params['spacer'] : true;
        $params['children'] = isset($params['children']) ? $params['children'] : false;
        $result = $this->getList($id, $params['children']);
        $result = static::formatTree($result, $params['key'], $params['value'], $params['spacer']);
        return $result;
    }

    /**
     * 转换成多维数组
     */
    public function getToChildren() {
        $params['children'] = isset($params['children']) ? $params['children'] : 'children';
        $result = static::toChildren($this->_data, $params['children']);
        return $result;
    }


    /**
     * 获取当前 pid 的子树
     * @param integer $pid
     * @param bool $self 是否包含自己
     */
    public function getChildren($pid, $self = false) {
        static $result;
        if ($result === null) {
            $result = $this->getList();
        }
        $children = $result->whereLoose('pid', $pid);

        $children->map(function ($child) use (&$children, $self) {
            $children = $children->merge($this->getChildren($child->id, $self));
        });

        if ($self) {
            $children = $children->merge($result->whereLoose('id', $pid));
        }
        return $children;
    }

    /**
     * 同时获取多个选择项的子集
     * @param $pids
     * @param bool $self
     * @return \Illuminate\Support\Collection|static
     */
    public function getChildrenMulti($pids, $self = false) {

        $children = collect();
        if(!is_array($pids)){
            return $children;
        }
        foreach ($pids as $pid) {
            $children = $children->merge($this->getChildren($pid, $self));
        }
        return $children;
    }
}