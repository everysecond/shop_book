<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseRegisterLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_register_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(0)->comment('统计类型：1:当天 2累计');
			$table->string('date', 64)->comment('统计的日期');
			$table->text('register_num_str', 65535)->comment('每小时注册数统计(24小时注册数以逗号分隔)');
			$table->bigInteger('total')->unsigned()->default(0)->comment('总注册数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->bigInteger('created_at')->default(0)->comment('创建时间');
			$table->bigInteger('updated_at')->default(0)->comment('更新时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_register_logs` comment '注册统计日志表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_register_logs');
	}

}
