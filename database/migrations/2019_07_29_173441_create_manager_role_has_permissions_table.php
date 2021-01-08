<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagerRoleHasPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_role_has_permissions', function(Blueprint $table)
		{
			$table->integer('role_id')->unsigned()->index('manager_role_has_permissions_role_id_foreign');
			$table->integer('permission_id')->unsigned();
			$table->primary(['permission_id','role_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_role_has_permissions');
	}

}
