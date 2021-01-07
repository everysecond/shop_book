<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('positions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('pid')->unsigned()->nullable()->default(0)->comment('上级id');
			$table->string('title', 100)->default('')->comment('职位');
			$table->integer('level')->unsigned()->default(0)->comment('级别');
			$table->integer('sort')->unsigned()->default(0)->comment('排序');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `positions` comment '职位表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('positions');
	}

}
