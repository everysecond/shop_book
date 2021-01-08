<?php

use Illuminate\Database\Seeder;

class ManagerPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('manager_permissions')->delete();
        
        \DB::table('manager_permissions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'pid' => 0,
                'level' => 0,
                'name' => '首页',
                'created_at' => NULL,
                'updated_at' => 1564136742,
            ),
            1 => 
            array (
                'id' => 6,
                'pid' => 1,
                'level' => 1,
                'name' => '控制台',
                'created_at' => 1563779474,
                'updated_at' => 1564136772,
            ),
            2 => 
            array (
                'id' => 35,
                'pid' => 0,
                'level' => 0,
                'name' => '系统管理',
                'created_at' => 1563878042,
                'updated_at' => 1564137186,
            ),
            3 => 
            array (
                'id' => 36,
                'pid' => 35,
                'level' => 1,
                'name' => '后台管理员',
                'created_at' => 1563878054,
                'updated_at' => 1564137107,
            ),
            4 => 
            array (
                'id' => 37,
                'pid' => 36,
                'level' => 2,
                'name' => '列表',
                'created_at' => 1563878071,
                'updated_at' => 1563878071,
            ),
            5 => 
            array (
                'id' => 40,
                'pid' => 6,
                'level' => 2,
                'name' => '查看',
                'created_at' => 1563878162,
                'updated_at' => 1564136795,
            ),
            6 => 
            array (
                'id' => 41,
                'pid' => 35,
                'level' => 1,
                'name' => '管理员角色',
                'created_at' => 1564137144,
                'updated_at' => 1564137144,
            ),
            7 => 
            array (
                'id' => 42,
                'pid' => 35,
                'level' => 1,
                'name' => '管理员权限',
                'created_at' => 1564137201,
                'updated_at' => 1564137201,
            ),
            8 => 
            array (
                'id' => 43,
                'pid' => 35,
                'level' => 1,
                'name' => '后台菜单',
                'created_at' => 1564137211,
                'updated_at' => 1564137211,
            ),
            9 => 
            array (
                'id' => 44,
                'pid' => 36,
                'level' => 2,
                'name' => '添加',
                'created_at' => 1564137763,
                'updated_at' => 1564137763,
            ),
            10 => 
            array (
                'id' => 45,
                'pid' => 36,
                'level' => 2,
                'name' => '修改',
                'created_at' => 1564137785,
                'updated_at' => 1564137785,
            ),
            11 => 
            array (
                'id' => 46,
                'pid' => 36,
                'level' => 2,
                'name' => '删除',
                'created_at' => 1564137802,
                'updated_at' => 1564137802,
            ),
            12 => 
            array (
                'id' => 47,
                'pid' => 36,
                'level' => 2,
                'name' => '授权',
                'created_at' => 1564137825,
                'updated_at' => 1564137825,
            ),
            13 => 
            array (
                'id' => 48,
                'pid' => 41,
                'level' => 2,
                'name' => '列表',
                'created_at' => 1564137863,
                'updated_at' => 1564137863,
            ),
            14 => 
            array (
                'id' => 49,
                'pid' => 41,
                'level' => 2,
                'name' => '添加',
                'created_at' => 1564137898,
                'updated_at' => 1564137898,
            ),
            15 => 
            array (
                'id' => 50,
                'pid' => 41,
                'level' => 2,
                'name' => '修改',
                'created_at' => 1564137944,
                'updated_at' => 1564137944,
            ),
            16 => 
            array (
                'id' => 51,
                'pid' => 41,
                'level' => 2,
                'name' => '删除',
                'created_at' => 1564137989,
                'updated_at' => 1564137989,
            ),
            17 => 
            array (
                'id' => 52,
                'pid' => 41,
                'level' => 2,
                'name' => '授权',
                'created_at' => 1564138004,
                'updated_at' => 1564138004,
            ),
            18 => 
            array (
                'id' => 53,
                'pid' => 42,
                'level' => 2,
                'name' => '列表',
                'created_at' => 1564138041,
                'updated_at' => 1564138041,
            ),
            19 => 
            array (
                'id' => 54,
                'pid' => 42,
                'level' => 2,
                'name' => '添加',
                'created_at' => 1564138070,
                'updated_at' => 1564138070,
            ),
            20 => 
            array (
                'id' => 55,
                'pid' => 42,
                'level' => 2,
                'name' => '修改',
                'created_at' => 1564138118,
                'updated_at' => 1564138118,
            ),
            21 => 
            array (
                'id' => 56,
                'pid' => 42,
                'level' => 2,
                'name' => '删除',
                'created_at' => 1564138169,
                'updated_at' => 1564138169,
            ),
            22 => 
            array (
                'id' => 57,
                'pid' => 43,
                'level' => 2,
                'name' => '列表',
                'created_at' => 1564138195,
                'updated_at' => 1564138195,
            ),
            23 => 
            array (
                'id' => 58,
                'pid' => 43,
                'level' => 2,
                'name' => '添加',
                'created_at' => 1564138228,
                'updated_at' => 1564138228,
            ),
            24 => 
            array (
                'id' => 59,
                'pid' => 43,
                'level' => 2,
                'name' => '修改',
                'created_at' => 1564138281,
                'updated_at' => 1564138281,
            ),
            25 => 
            array (
                'id' => 60,
                'pid' => 43,
                'level' => 2,
                'name' => '删除',
                'created_at' => 1564138302,
                'updated_at' => 1564138302,
            ),
        ));
        
        
    }
}