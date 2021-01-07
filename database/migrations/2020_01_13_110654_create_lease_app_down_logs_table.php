<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseAppDownLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_app_down_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('app_type')->default(0)->comment('app类型：1:租点用户端 2网点用户端');
			$table->string('date', 64)->default('')->comment('统计的日期');
			$table->text('channel_json', 65535)->nullable()->comment('下载渠道统计json串');
			$table->integer('total')->default(0)->comment('当日下载总量');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_app_down_logs` comment '提前续租数统计'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_app_down_logs');
	}

}
