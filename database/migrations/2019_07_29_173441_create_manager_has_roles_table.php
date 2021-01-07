<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagerHasRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_has_roles', function(Blueprint $table)
		{
			$table->integer('manager_id')->unsigned()->index('manager_has_roles_manager_id_model_type_index');
			$table->integer('role_id')->unsigned();
			$table->primary(['role_id','manager_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_has_roles');
	}

}
