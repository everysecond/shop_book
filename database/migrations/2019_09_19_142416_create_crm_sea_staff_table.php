<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmSeaStaffTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_sea_staff', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sea_id')->unsigned()->nullable()->default(0)->comment('职位id');
			$table->integer('staff_id')->unsigned()->nullable()->default(0)->comment('职员id');
			$table->boolean('can_assign')->default(1)->comment('分配权限 1不可分配 2可分配');
			$table->boolean('can_get')->default(1)->comment('领取权限 1不可领取 2可领取');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_sea_staff` comment '公海管理人员表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_sea_staff');
	}

}
