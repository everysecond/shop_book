<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ManagersTableSeeder::class);
        $this->call(ManageMenusTableSeeder::class);
        $this->call(ManagerRolesTableSeeder::class);
        $this->call(ManagerHasRolesTableSeeder::class);
        $this->call(ManagerPermissionActionsTableSeeder::class);
        $this->call(ManagerPermissionsTableSeeder::class);
    }
}
