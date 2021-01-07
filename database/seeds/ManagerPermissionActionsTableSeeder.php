<?php

use Illuminate\Database\Seeder;

class ManagerPermissionActionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('manager_permission_actions')->delete();
        
        \DB::table('manager_permission_actions')->insert(array (
            0 => 
            array (
                'permission_id' => 37,
                'action' => 'manager.index',
            ),
            1 => 
            array (
                'permission_id' => 37,
                'action' => 'manager.paginate',
            ),
            2 => 
            array (
                'permission_id' => 40,
                'action' => 'manage.console',
            ),
            3 => 
            array (
                'permission_id' => 40,
                'action' => 'manage.index',
            ),
            4 => 
            array (
                'permission_id' => 44,
                'action' => 'manager.create',
            ),
            5 => 
            array (
                'permission_id' => 44,
                'action' => 'manager.store',
            ),
            6 => 
            array (
                'permission_id' => 45,
                'action' => 'manager.edit',
            ),
            7 => 
            array (
                'permission_id' => 45,
                'action' => 'manager.update',
            ),
            8 => 
            array (
                'permission_id' => 46,
                'action' => 'manager.destroy',
            ),
            9 => 
            array (
                'permission_id' => 47,
                'action' => 'manager.permission',
            ),
            10 => 
            array (
                'permission_id' => 48,
                'action' => 'manager-role.index',
            ),
            11 => 
            array (
                'permission_id' => 49,
                'action' => 'manager-role.create',
            ),
            12 => 
            array (
                'permission_id' => 49,
                'action' => 'manager-role.store',
            ),
            13 => 
            array (
                'permission_id' => 50,
                'action' => 'manager-role.edit',
            ),
            14 => 
            array (
                'permission_id' => 50,
                'action' => 'manager-role.update',
            ),
            15 => 
            array (
                'permission_id' => 51,
                'action' => 'manager-role.destroy',
            ),
            16 => 
            array (
                'permission_id' => 52,
                'action' => 'manager-role.permission',
            ),
            17 => 
            array (
                'permission_id' => 53,
                'action' => 'manager-permission.index',
            ),
            18 => 
            array (
                'permission_id' => 54,
                'action' => 'manager-permission.create',
            ),
            19 => 
            array (
                'permission_id' => 54,
                'action' => 'manager-permission.store',
            ),
            20 => 
            array (
                'permission_id' => 55,
                'action' => 'manager-permission.edit',
            ),
            21 => 
            array (
                'permission_id' => 55,
                'action' => 'manager-permission.update',
            ),
            22 => 
            array (
                'permission_id' => 56,
                'action' => 'manager-permission.destroy',
            ),
            23 => 
            array (
                'permission_id' => 57,
                'action' => 'manage-menu.index',
            ),
            24 => 
            array (
                'permission_id' => 58,
                'action' => 'manage-menu.create',
            ),
            25 => 
            array (
                'permission_id' => 58,
                'action' => 'manage-menu.icon',
            ),
            26 => 
            array (
                'permission_id' => 58,
                'action' => 'manage-menu.store',
            ),
            27 => 
            array (
                'permission_id' => 59,
                'action' => 'manage-menu.change',
            ),
            28 => 
            array (
                'permission_id' => 59,
                'action' => 'manage-menu.edit',
            ),
            29 => 
            array (
                'permission_id' => 59,
                'action' => 'manage-menu.icon',
            ),
            30 => 
            array (
                'permission_id' => 59,
                'action' => 'manage-menu.update',
            ),
            31 => 
            array (
                'permission_id' => 60,
                'action' => 'manage-menu.destroy',
            ),
        ));
        
        
    }
}