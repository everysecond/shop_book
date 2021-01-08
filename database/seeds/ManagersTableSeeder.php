<?php

use Illuminate\Database\Seeder;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DB::table('managers')->where('username', 'admin')->doesntExist()) {
            DB::table('managers')->insert([
                'mobile' => '00000000000',
                'name' => '管理员',
                'username' => 'admin',
                'password' => bcrypt('123456'),
                'status' => 1,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        }
    }
}
