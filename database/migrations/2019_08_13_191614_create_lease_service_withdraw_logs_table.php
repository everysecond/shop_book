<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceWithdrawLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_withdraw_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('json_amount', 65535)->nullable()->comment('提现金额 ');
			$table->string('date', 10)->nullable()->comment('统计的日期');
			$table->text('json_num', 65535)->nullable()->comment('提现次数');
			$table->integer('total')->unsigned()->default(0)->comment('当日总租赁数');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_withdraw_logs` comment '提现统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_withdraw_logs');
	}

}
