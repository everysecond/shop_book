<?php

return [
    //需要过滤的路由  不需要验证的路由
    'fill_route' => [
        'logout', //退出
        'crm.upload', //上传
        'manage.index', //获取菜单
        'agent.search', //查询区域联动
        'cus.search', //客户模糊搜索
        'cus.change.search', //查询客户关联信息

        //中台移动端
        'api.timeList', //时间选择器
        'api.menus', //移动端菜单
        'api.managerMenu', //移动端个人省份权限菜单
        'api.siteMenu', //移动端个人站点权限菜单
    ]
];
