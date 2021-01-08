<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagerHasPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_has_permissions', function(Blueprint $table)
		{
			$table->integer('manager_id')->unsigned()->index('manager_has_permissions_manager_id_model_type_index');
			$table->integer('permission_id')->unsigned();
			$table->primary(['permission_id','manager_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_has_permissions');
	}

}
