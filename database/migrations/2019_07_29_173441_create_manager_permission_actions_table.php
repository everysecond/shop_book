<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagerPermissionActionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_permission_actions', function(Blueprint $table)
		{
			$table->integer('permission_id')->unsigned()->default(0);
			$table->string('action')->default('');
			$table->primary(['permission_id','action']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_permission_actions');
	}

}
