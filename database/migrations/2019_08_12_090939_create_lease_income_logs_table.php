<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseIncomeLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_income_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('date', 10)->nullable()->comment('统计的日期');
			$table->text('json', 65535)->nullable()->comment('各省份收益json串');
			$table->integer('total')->unsigned()->default(0)->comment('当日总租赁数');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_income_logs` comment '网点收益统计日志表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_income_logs');
	}

}
