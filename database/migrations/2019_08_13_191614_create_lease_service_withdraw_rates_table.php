<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceWithdrawRatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_withdraw_rates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('service_id')->unsigned()->default(0)->comment('用户ID');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('province_id')->unsigned()->default(0)->comment('省id');
			$table->integer('rate_num')->default(0)->comment('距上次提现间隔天数');
			$table->integer('agent_id')->unsigned()->default(0)->comment('区域id');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_withdraw_rates` comment '提现频率表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_withdraw_rates');
	}

}
