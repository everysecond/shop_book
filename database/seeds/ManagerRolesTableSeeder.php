<?php

use Illuminate\Database\Seeder;

class ManagerRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('manager_roles')->delete();
        
        \DB::table('manager_roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => '超管',
                'code' => 'super',
                'created_at' => 1563445404,
                'updated_at' => 1563445404,
            ),
        ));
        
        
    }
}