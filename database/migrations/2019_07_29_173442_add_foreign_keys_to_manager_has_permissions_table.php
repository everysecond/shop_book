<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToManagerHasPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manager_has_permissions', function(Blueprint $table)
		{
			$table->foreign('permission_id')->references('id')->on('manager_permissions')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('manager_has_permissions', function(Blueprint $table)
		{
			$table->dropForeign('manager_has_permissions_permission_id_foreign');
		});
	}

}
