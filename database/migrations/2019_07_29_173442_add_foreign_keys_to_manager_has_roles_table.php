<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToManagerHasRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('manager_has_roles', function(Blueprint $table)
		{
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
		Schema::table('manager_has_roles', function(Blueprint $table)
		{
			$table->dropForeign('manager_has_roles_role_id_foreign');
		});
	}

}
