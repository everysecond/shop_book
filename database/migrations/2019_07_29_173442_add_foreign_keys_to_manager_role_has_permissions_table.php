<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToManagerRoleHasPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manager_role_has_permissions', function(Blueprint $table)
		{
			$table->foreign('permission_id')->references('id')->on('manager_permissions')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('role_id')->references('id')->on('manager_roles')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manager_role_has_permissions', function(Blueprint $table)
		{
			$table->dropForeign('manager_role_has_permissions_permission_id_foreign');
			$table->dropForeign('manager_role_has_permissions_role_id_foreign');
		});
	}

}
