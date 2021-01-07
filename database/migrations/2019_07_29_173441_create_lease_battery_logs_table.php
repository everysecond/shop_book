<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseBatteryLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_battery_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('date', 10)->nullable()->comment('统计的日期');
			$table->integer('model_one')->unsigned()->nullable()->default(0)->comment('48V12A租赁数');
			$table->integer('model_two')->unsigned()->nullable()->default(0)->comment('48V20A租赁数');
			$table->integer('model_three')->unsigned()->nullable()->default(0)->comment('48V32A租赁数');
			$table->integer('model_four')->unsigned()->nullable()->default(0)->comment('60V20A租赁数');
			$table->integer('model_five')->unsigned()->nullable()->default(0)->comment('72V20A租赁数');
			$table->integer('other')->unsigned()->nullable()->default(0)->comment('其它电池型号租赁数');
			$table->integer('total')->unsigned()->default(0)->comment('当日总租赁数');
			$table->integer('province_id')->unsigned()->default(0)->comment('省');
			$table->integer('city_id')->unsigned()->default(0)->comment('市');
			$table->integer('county_id')->unsigned()->default(0)->comment('区县');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_battery_logs` comment '电池型号记录表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_battery_logs');
	}

}
