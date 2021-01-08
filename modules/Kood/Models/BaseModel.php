<?php

namespace Modules\Kood\Models;

use Auth;
use App\Common\Picture;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Common\Helpers\Tree;
use Illuminate\Database\Eloquent\Relations\Relation;
use Symfony\Component\HttpFoundation\File\File;
use App\Common\Exceptions\AjaxException;

class BaseModel extends Model {
    protected $connection = "mysql_kd";
    const ON = 1;
    const OFF = 0;

    const TIME_SCOPE = 86400 * 31;

    const PAGE_SIZE = 20;

    /**
     * 场景字段 用于批量赋值
     * @var Array
     */
    protected $scenarios = [];

    /**
     * 返回显示字段
     * @var Array
     */
    protected $fields = [];


    public $scopeSite = null;

    /**
     * 返回的当前模型
     */
    public static function instance() {
        static $instance;
        if (is_null($instance)) {
            $instance = new static;
        }
        return $instance;
    }

    /**
     * 内置过滤器
     */
    public function validateFilter($data, $senario) {
        return $data;
    }

    /**
     * 默认规则
     */
    public function validateRules($request) {
        return [
            'default' => [],
        ];
    }

    /*
     * 默认返回信息
     */
    public function validateMessage() {
        return ['default' => []];
    }

    /**
     * 获取验证规则
     */
    public function getRules($request, $scenario) {
        $defaultRules = $scenarioRules = [];
        $rules = $this->validateRules($request);
        if (isset($rules['default'])) {
            $defaultRules = $rules['default'];
        }
        if (isset($rules[$scenario])) {
            $scenarioRules = $rules[$scenario];
        }
        $defaultRules = array_merge($defaultRules, $scenarioRules);
        return $defaultRules;
    }

    /**
     * 获取验证提示信息
     */
    public function getMessages($scenario) {
        $defaultMessages = $scenarioMessages = [];
        $messages = $this->validateMessage();
        if (isset($messages['default'])) {
            $defaultMessages = $messages['default'];
        }
        if (isset($messages[$scenario])) {
            $scenarioMessages = $messages[$scenario];
        }
        $defaultMessages = array_merge($defaultMessages, $scenarioMessages);
        return $defaultMessages;
    }

    /**
     * 场景字段
     */
    public function getScenarios($scenario) {
        $defaultScenarios = $scenarios = [];
        if (isset($this->scenarios['default'])) {
            $defaultScenarios = $this->scenarios['default'];
        }
        if (isset($this->scenarios[$scenario])) {
            $scenarios = $this->scenarios[$scenario];
        }
        $defaultScenarios = array_merge($defaultScenarios, $scenarios);
        $defaultScenarios = array_unique($defaultScenarios);
        return $defaultScenarios;
    }

    /**
     * 获取显示的字段 可使用 makeVisible
     * @param Array $fields $this->fields 下标
     * @param String | Array $appends 追加显示
     * @return array
     */
    public function getFields($fields, $appends = []) {
        $data = $result = [];
        foreach ((array)$fields as $field) {
            $data = array_merge($data, $this->fields[$field]);
        }
        $data = array_merge($data, (array)$appends);
        $data = array_unique($data);
        foreach ($data as $key) {
            $result[$key] = $this->$key;
        }
        return $result;
    }

    /**
     * 默认排序
     * @return mixed
     */
    public function scopeSort($query) {
        return $query->orderBy('sort', 'DESC')->orderBy('id', 'ASC');
    }

    /**
     * 按创建时间降序排序
     * @param $query
     * @return mixed
     */
    public function scopeSortCreatedAt($query) {
        return $query->orderBy('created_at', 'DESC')->orderBy('id', 'DESC');
    }

    /**
     * 获取树的实例 支持
     * @param $query
     * @return Object
     */
    public function scopeTree($query) {
        $result = $query->orderBy('pid', 'ASC')->orderBy('sort', 'DESC')->orderBy('id', 'ASC')->get();
        return new Tree($result);
    }


    /**
     * 搜索 created_at
     * @param $query
     * @param int|string $start
     * @param int|string $end
     */
    public function scopeSearchCreatedAt($query, $start = 0, $end = 0) {
        $start = strtotime($start);
        $end = $end ? strtotime($end) + 24 * 60 * 60 : time();

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }
    }

    /**
     * 获取模型默认值
     * @return array
     */
    protected function getDefaultAttribute() {
        $result = \DB::select('SHOW COLUMNS FROM ' . static::getPrefix() . static::getTable());
        $collect = collect([]);
        foreach ($result as $val) {
            $val = get_object_vars($val);
            $collect->put($val['Field'], $val['Default']);
        }
        return $collect->toArray();
    }

    /**
     * 获取表前缀
     * @return mixed
     */
    public static function getPrefix() {
        return config('database.connections.mysql.prefix');
    }

    /**
     * 设置模型默认值
     */
    public function setDefault() {
        $result = $this->getDefaultAttribute();
        foreach ($result as $key => $val) {
            $this->attributes[$key] = $val;
        }
        return $this;
    }

    /**
     * 简单图片上传
     * @param File $file
     * @param $name
     * @return string
     */
//    public function upload(File $file, $name = null) {
//        $upload = new Upload();
//        return $upload->image($file, ['png', 'jpg', 'gif', 'jpeg'], 4096);
//    }


    /**
     * 默认图片
     * @param $picture
     * @return mixed
     */
    public function getPictureAttribute($picture) {
        if (!$picture) {
            return new Picture();
        } else {
            return new Picture($picture);
        }
    }

    /**
     * 默认头像
     * @param $avatar
     * @return mixed
     */
    public function getAvatarAttribute($avatar) {
        if (!$avatar) {
            return new Picture();
        } else {
            return new Picture($avatar);
        }
    }


    /**
     * 更新子节点的深度
     * @param Int $deep
     * @param Array $params
     */
    public function updateChildrenDeep($deep) {
        if ($deep) {
            $children = $this->tree()->getSelect($this->id, ['children' => true, 'value' => 'id']);
            $this->whereIn('id', $children)->decrement('deep', $deep);
        }
    }

    /**
     * 原子错误
     * @var
     */
    protected
        $error;

    /**
     * 添加原子错误信息
     * @param $error
     */
    public function addError($error = '') {
        $this->error = $error;
    }

    /**
     * 验证状态是否允许操作
     * @param $model
     * @throws AjaxException
     */
    public function checkAction($field, $message = '当前状态不允许该操作') {
        if (!$this->{$field}) {
            throw new AjaxException($this->error ?: $message);
        }
        //unset($this->{$field});
    }


    /**
     * @param $id
     * @param string $message
     * @return mixed|BaseModel
     * @throws AjaxException
     */
    public static function loadModel($id, $message = '没有找到数据') {
        $model = static::find($id);
        if (!$model) {
            throw new AjaxException(value($message));
        }

        return $model;
    }

    /**
     * 重写父类__get方法
     * 如果获取的属性为关联关系,当关联关系没找到的时候,返回一个空模型,防止报错
     * @param string $key
     * @return \Illuminate\Support\Collection|mixed
     */
    public function __get($key) {
        $value = parent::getAttribute($key);
        if (is_null($value)) {
            if (method_exists($this, $key)) {
                $relation = $this->{$key}();
                if ($relation instanceof Relation) {
                    $related = $relation->getRelated();
                    switch (class_basename($relation)) {
                        case 'BelongsTo':
                        case 'HasOne':
                            return $related->instance();
                        case 'HasMany':
                        case 'BelongsToMany':
                            return collect([$related->instance()]);
                        default:
                            return collect([$related->instance()]);
                    }
                }
            }
        }
        return $value;
    }


    /**
     * 判断当时模型是否为空模型
     * 用ID为null或者为0 进行判断,后期可优化
     * @return bool
     */
    public function isNull() {
        return !!!$this->getAttributes('id');
    }

    /**
     * 关联查询字段
     * @param $query
     * @param $relation
     * @param array $columns
     * @return mixed
     */
    public function scopeWithOnly($query, $relation, Array $columns) {
        return $query->with([$relation => function ($query) use ($columns) {
            $query->select(array_merge(['id'], $columns));
        }]);
    }

    public function scopeVip($query, $vip = 0) {
        return $query->where('vip', '>=', $vip);
    }

    public function relationAll($related) {
        $instance = new $related;
        return new \App\Common\RelationAll($instance->newQuery(), $this);
    }
}
