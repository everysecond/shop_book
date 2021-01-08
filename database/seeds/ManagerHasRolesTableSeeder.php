<?php

use Illuminate\Database\Seeder;

class ManagerHasRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('manager_has_roles')->delete();
        
        \DB::table('manager_has_roles')->insert(array (
            0 => 
            array (
                'manager_id' => 1,
                'role_id' => 1,
            ),
        ));
        
        
    }
}