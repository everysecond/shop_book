<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseStartLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_start_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(0)->comment('统计类型：1:当天 2累计');
			$table->string('date', 64)->nullable()->comment('统计的日期');
			$table->text('start_num_str', 65535)->nullable()->comment('0-24点分时段启动数');
			$table->bigInteger('total')->unsigned()->default(0)->comment('总启动数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->bigInteger('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->bigInteger('updated_at')->unsigned()->default(0)->comment('更新时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_start_logs` comment '启动统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_start_logs');
	}

}
