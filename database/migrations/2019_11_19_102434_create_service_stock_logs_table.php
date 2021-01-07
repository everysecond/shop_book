<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceStockLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_stock_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('date', 10)->nullable()->comment('统计的日期');
			$table->text('json_data', 65535)->nullable()->comment('统计数据');
			$table->bigInteger('created_at')->default(0);
			$table->bigInteger('updated_at')->default(0);
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `service_stock_logs` comment '网点库存每日记录'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('service_stock_logs');
	}

}
