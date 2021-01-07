<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => '您必须接受 :attribute。',
    'active_url'           => ':attribute 不是一个有效的URL。',
    'after'                => ':attribute 必须是:date之后的日期。',
    'alpha'                => ':attribute 只能包含字母。',
    'alpha_dash'           => ':attribute 只能包含字母和数字，以及破折号和下划线。',
    'alpha_num'            => ':attribute 必须是字母或数字。',
    'array'                => ':attribute 必须是数组',
    'before'               => ':attribute 必须是:date之前的日期。',
    'between'              => [
        'numeric' => ':attribute 必须是在:min到:max之间的数。',
        'file'    => ':attribute 的大小必须在:min到:max千字节之间。',
        'string'  => ':attribute 的长度必须在:min到:max之间。',
        'array'   => ':attribute 的元素个数必须在:min到:max之间。',
    ],
    'boolean'              => ':attribute 必须是“true”或“false”。',
    'confirmed'            => ':attribute 确认不匹配。',
    'date'                 => ':attribute 不是一个有效的日期。',
    'date_format'          => ':attribute 不是“:format”格式的日期。',
    'different'            => ':attribute 不能是:other',
    'digits'               => ':attribute 必须是长度为长度为:digits的数字。',
    'digits_between'       => ':attribute 必须是长度必须介于:min和:max之间的数字。',
    'dimensions'           => ':attribute 尺寸不符合要求。',
    'distinct'             => ':attribute 不能包含重复的值。',
    'email'                => ':attribute 不是一个有效的邮箱地址',
    'exists'               => ':attribute 不存在。',
    'file'                 => ':attribute 不是一个有效的文件。',
    'filled'               => ':attribute 不能为空',
    'image'                => ':attribute 不是图片',
    'in'                   => ':attribute 不是一个有效值。',
    'in_array'             => ':attribute 不是一个有效值。',
    'integer'              => ':attribute 必须是整型。',
    'ip'                   => ':attribute 必须是IP地址。',
    'json'                 => ':attribute 必须是有效的JSON字符串。',
    'max'                  => [
        'numeric' => ':attribute 不能大于:max。',
        'file'    => ':attribute 的大小不能大于:max千字节。',
        'string'  => ':attribute 的长度不能大于:max。',
        'array'   => ':attribute 的元素个数不能大于:max个。',
    ],
    'mimes'                => ':attribute 不是有效的文件类型:values.',
    'min'                  => [
        'numeric' => ':attribute 不能小于:min。',
        'file'    => ':attribute 的大小不能小于:min千字节。',
        'string'  => ':attribute 的长度不能小于:min。',
        'array'   => ':attribute 的元素个数不能小于:min个。',
    ],
    'not_in'               => ':attribute 无效。',
    'numeric'              => ':attribute 必须是一个数字。',
    'present'              => ':attribute 必须存在。',
    'regex'                => ':attribute 无效。',
    'required'             => ':attribute 不能为空。',
    'required_if'          => ':attribute 不能为空（当:other为:value时）。',
    'required_unless'      => ':attribute 不能为空（除非:other为:values时）。',
    'required_with'        => ':attribute 不能为空（:values存在时）。',
    'required_with_all'    => ':attribute 不能为空（:values全部存在时）。',
    'required_without'     => ':attribute 不能为空（:values不存在时）。',
    'required_without_all' => ':attribute 不能为空（:values全部不存在时）。',
    'same'                 => ':attribute 和:other不匹配。',
    'size'                 => [
        'numeric' => ':attribute 不等于:size。',
        'file'    => ':attribute 的大小必须是:size千字节。',
        'string'  => ':attribute 的长度必须是:size。',
        'array'   => ':attribute 的元素必须是:size个。',
    ],
    'string'               => ':attribute 必须是字符串。',
    'timezone'             => ':attribute 不是有效的十区。',
    'unique'               => ':attribute 已存在。',
    'url'                  => ':attribute 不是一个URL地址。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
