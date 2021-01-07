<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseProcessHoursTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_process_hours', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->date('process_date')->index('renewal_date')->comment('日期（例如 2019-7-10）');
			$table->boolean('insert_hour')->default(0)->comment('所在时间段（例如 1 2 3...）');
			$table->integer('login_num')->default(0)->comment('到达登录页');
			$table->integer('index_num')->default(0)->comment('到达首页');
			$table->integer('scan_num')->default(0)->comment('到达扫码页');
			$table->integer('detail_num')->default(0)->comment('到达租赁详情');
			$table->integer('period_num')->default(0)->comment('选择租赁周期');
			$table->integer('deduction_num')->default(0)->comment('选择旧电池抵扣');
			$table->integer('submit_lease_num')->default(0)->comment('提交租赁单');
			$table->integer('business_num')->default(0)->comment('商家确认');
			$table->integer('topay_num')->default(0)->comment('租赁待支付');
			$table->integer('dopay_num')->default(0)->comment('发起支付');
			$table->integer('pay_num')->default(0)->comment('支付租赁');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable()->default(0);
			$table->bigInteger('updated_at')->nullable();
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_process_hours` comment '(每小时)租赁到达转化流程记录表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_process_hours');
	}

}
