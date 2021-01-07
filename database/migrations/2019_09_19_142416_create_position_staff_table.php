<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePositionStaffTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('position_staff', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('position_id')->unsigned()->nullable()->default(0)->comment('职位id');
			$table->integer('staff_id')->unsigned()->nullable()->default(0)->comment('职员id');
			$table->boolean('see_level')->default(0)->comment('查看权限 0查看本人及夏季 1查看同级及下级');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `position_staff` comment '职位人员表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('position_staff');
	}

}
