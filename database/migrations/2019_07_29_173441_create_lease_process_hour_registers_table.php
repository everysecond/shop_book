<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseProcessHourRegistersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_process_hour_registers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->date('process_date')->index('renewal_date')->comment('日期（例如 2019-7-10）');
			$table->boolean('insert_hour')->default(0)->comment('所在时间段（例如 1 2 3...）');
			$table->integer('login_num')->default(0)->comment('登录成功用户数');
			$table->integer('index_num')->default(0)->comment('到达首页用户数');
			$table->integer('scan_num')->default(0)->comment('发起扫码用户数');
			$table->integer('detail_num')->default(0)->comment('到达租赁详情页用户数');
			$table->integer('period_num')->default(0)->comment('选择租赁周期用户数');
			$table->integer('deduction_num')->default(0)->comment('选择旧电池抵扣用户数');
			$table->integer('submit_lease_num')->default(0)->comment('提交租赁单用户数');
			$table->integer('mylease_num')->nullable()->default(0)->comment('我的租赁页面用户数');
			$table->integer('business_num')->default(0)->comment('商家确认扫码用户数');
			$table->integer('topay_num')->default(0)->comment('租赁待支付页面用户数');
			$table->integer('dopay_num')->default(0)->comment('发起支付用户数');
			$table->integer('pay_num')->default(0)->comment('支付成功页面用户数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable()->default(0);
			$table->bigInteger('updated_at')->nullable()->default(0);
			$table->softDeletes();
			$table->integer('register_num')->nullable()->default(0)->comment('注册成功用户数');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_process_hour_registers` comment '当天注册并且租赁-记录表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_process_hour_registers');
	}

}
