<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmOpenSeasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_open_seas', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100)->default('')->comment('职位');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_open_seas` comment '公海管理表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_open_seas');
	}

}
