<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseStartTerminalLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_start_terminal_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('date', 64)->nullable()->comment('统计的日期');
			$table->bigInteger('user_ios_num')->unsigned()->default(0)->comment('用户端ios启动数');
			$table->bigInteger('user_android_num')->unsigned()->default(0)->comment('用户端安卓启动数');
			$table->bigInteger('web_ios_num')->unsigned()->default(0)->comment('网点端ios启动数');
			$table->bigInteger('web_android_num')->unsigned()->default(0)->comment('网点端安卓启动数');
			$table->bigInteger('depot_android_num')->unsigned()->default(0)->comment('仓库端安卓启动数');
			$table->bigInteger('flow_android_num')->unsigned()->default(0)->comment('物流端安卓启动数');
			$table->bigInteger('factory_android_num')->unsigned()->default(0)->comment('工厂端安卓启动数');
			$table->bigInteger('total')->unsigned()->default(0)->comment('总启动数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->bigInteger('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->bigInteger('updated_at')->unsigned()->default(0)->comment('更新时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_start_terminal_logs` comment '启动终端统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_start_terminal_logs');
	}

}
