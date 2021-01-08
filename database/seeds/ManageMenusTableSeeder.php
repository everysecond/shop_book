<?php

use Illuminate\Database\Seeder;

class ManageMenusTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('manage_menus')->delete();
        
        \DB::table('manage_menus')->insert(array (
            0 => 
            array (
                'id' => 1,
                'pid' => 0,
                'name' => '首页',
                'route' => 'manage.console',
                'sort' => 0,
                'icon' => 'layui-icon-home',
                'status' => 1,
                'level' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'pid' => 0,
                'name' => '系统管理',
                'route' => '',
                'sort' => 10,
                'icon' => 'layui-icon-set',
                'status' => 1,
                'level' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'pid' => 2,
                'name' => '后台管理员',
                'route' => 'manager.index',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'pid' => 2,
                'name' => '管理员权限',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'pid' => 2,
                'name' => '后台菜单',
                'route' => 'manage-menu.index',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'pid' => 4,
                'name' => '角色',
                'route' => 'manager-role.index',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'pid' => 4,
                'name' => '权限',
                'route' => 'manager-permission.index',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            7 => 
            array (
                'id' => 10,
                'pid' => 9,
                'name' => '概况',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            8 => 
            array (
                'id' => 11,
                'pid' => 10,
                'name' => '今日指标',
                'route' => 'lease.reports.view.portrayal',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            9 => 
            array (
                'id' => 12,
                'pid' => 0,
                'name' => '租点用户端',
                'route' => '',
                'sort' => 1,
                'icon' => 'layui-icon-app',
                'status' => 1,
                'level' => 1,
            ),
            10 => 
            array (
                'id' => 13,
                'pid' => 12,
                'name' => '概况',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            11 => 
            array (
                'id' => 14,
                'pid' => 13,
                'name' => '今日指标',
                'route' => 'lease.reports.view.dashboard.today',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            12 => 
            array (
                'id' => 15,
                'pid' => 13,
                'name' => '整体趋势',
                'route' => 'lease.reports.view.dashboard.total',
                'sort' => 1,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            13 => 
            array (
                'id' => 16,
                'pid' => 12,
                'name' => '用户数据',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            14 => 
            array (
                'id' => 17,
                'pid' => 16,
                'name' => '用户画像',
                'route' => 'lease.reports.view.portrayal',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            15 => 
            array (
                'id' => 18,
                'pid' => 16,
                'name' => '注册数据',
                'route' => 'lease.reports.view.register',
                'sort' => 1,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            16 => 
            array (
                'id' => 19,
                'pid' => 16,
                'name' => '启动次数',
                'route' => 'lease.reports.view.start',
                'sort' => 2,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            17 => 
            array (
                'id' => 20,
                'pid' => 12,
                'name' => '租赁业务',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            18 => 
            array (
                'id' => 21,
                'pid' => 20,
                'name' => '新用户租赁',
                'route' => 'lease.reports.view.lease.new',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            19 => 
            array (
                'id' => 22,
                'pid' => 20,
                'name' => '老用户租赁',
                'route' => 'lease.reports.view.lease.old',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            20 => 
            array (
                'id' => 23,
                'pid' => 20,
                'name' => '电池型号',
                'route' => 'lease.reports.view.battery',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            21 => 
            array (
                'id' => 24,
                'pid' => 12,
                'name' => '其他业务',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            22 => 
            array (
                'id' => 25,
                'pid' => 24,
                'name' => '续租数据',
                'route' => 'lease.renewal.show',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            23 => 
            array (
                'id' => 26,
                'pid' => 24,
                'name' => '退租数据',
                'route' => 'lease.rebate.show',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            24 => 
            array (
                'id' => 27,
                'pid' => 24,
                'name' => '换组数据',
                'route' => 'lease.rent_change.show',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            25 => 
            array (
                'id' => 28,
                'pid' => 12,
                'name' => '保险数据',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            26 => 
            array (
                'id' => 29,
                'pid' => 28,
                'name' => '投保分析',
                'route' => 'lease.insurance.show',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            27 => 
            array (
                'id' => 30,
                'pid' => 28,
                'name' => '电池报失',
                'route' => 'lease.loss_battery.show',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            28 => 
            array (
                'id' => 31,
                'pid' => 12,
                'name' => '运营报表',
                'route' => '',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            29 => 
            array (
                'id' => 32,
                'pid' => 31,
                'name' => '续租客户来源',
                'route' => 'lease.data_report.renewal_customer',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            30 => 
            array (
                'id' => 33,
                'pid' => 31,
                'name' => '到期-续租周期',
                'route' => 'lease.data_report.expire_renewal',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            31 => 
            array (
                'id' => 34,
                'pid' => 31,
                'name' => '退租-租赁周期',
                'route' => 'lease.data_report.rebate_rent',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            32 => 
            array (
                'id' => 35,
                'pid' => 20,
                'name' => '新老用户租赁对比',
                'route' => 'lease.reports.leasenewold',
                'sort' => 0,
                'icon' => '&#xe6b2;',
                'status' => 1,
                'level' => 1,
            ),
            33 => 
            array (
                'id' => 36,
                'pid' => 20,
                'name' => '注册-租赁',
                'route' => 'lease.reports.leaseregister',
                'sort' => 0,
                'icon' => 'layui-icon-file-b',
                'status' => 1,
                'level' => 1,
            ),
            34 => 
            array (
                'id' => 37,
                'pid' => 20,
                'name' => '登录-租赁',
                'route' => 'lease.reports.leaseprocessshow',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            35 => 
            array (
                'id' => 38,
                'pid' => 20,
                'name' => '租赁到期转化',
                'route' => 'lease.reports.leasematurity',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            36 => 
            array (
                'id' => 39,
                'pid' => 20,
                'name' => '租赁区域',
                'route' => 'lease.reports.leasearea',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            37 => 
            array (
                'id' => 40,
                'pid' => 20,
                'name' => '渠道转化',
                'route' => 'lease.reports.leasearea',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
            38 => 
            array (
                'id' => 41,
                'pid' => 31,
                'name' => '注册-租赁周期',
                'route' => 'lease.reports.leaseregisterperiod',
                'sort' => 0,
                'icon' => '',
                'status' => 1,
                'level' => 1,
            ),
        ));
        
        
    }
}