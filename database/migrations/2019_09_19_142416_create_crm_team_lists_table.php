<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmTeamListsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_team_lists', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->comment('人员ID');
			$table->boolean('team_role')->comment('1:负责人 2：协作人员;');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
			$table->integer('customer_id')->index('customer_id')->comment('客户ID');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_team_lists` comment '客户归属团队管理表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_team_lists');
	}

}
