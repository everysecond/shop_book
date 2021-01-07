<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeasePaymentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_payments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('order_no', 20)->default('')->unique('order_no')->comment('订单号');
			$table->integer('contract_id')->unsigned()->default(0)->index()->comment('合约ID');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('用户ID');
			$table->boolean('type')->default(0)->comment('操作类型：1:新租 2续租 3续约 4换租 5不同型号换租');
			$table->boolean('term_index')->default(0)->comment('当前期数');
			$table->boolean('lease_term')->default(0)->comment('租期');
			$table->decimal('deposit', 10)->unsigned()->default(0.00)->comment('押金');
			$table->decimal('rental', 10)->unsigned()->default(0.00)->comment('租金');
			$table->decimal('pre_balance', 10)->unsigned()->default(0.00)->comment('预付款余额');
			$table->decimal('install_fee', 10)->unsigned()->default(0.00)->comment('安装费');
			$table->decimal('total_amount', 10)->unsigned()->default(0.00)->comment('总金额');
			$table->decimal('coupon_amount', 10)->unsigned()->default(0.00)->comment('优惠券抵扣金额');
			$table->decimal('prepayment_amount', 10)->unsigned()->default(0.00)->comment('预付款支付金额');
			$table->decimal('balance_amount', 10)->unsigned()->default(0.00)->comment('余额抵扣金额');
			$table->decimal('recycle_amount', 10)->unsigned()->default(0.00)->comment('回收抵扣金额');
			$table->decimal('recycle_balance', 10)->unsigned()->default(0.00)->comment('回收抵扣结余');
			$table->decimal('amount', 10)->unsigned()->default(0.00)->comment('实付金额');
			$table->boolean('status')->default(0)->comment('状态 0:待支付 1:已支付 2:已取消');
			$table->timestamps();
			$table->dateTime('payed_at')->nullable()->comment('付款时间');
			$table->string('trade_no', 50)->default('')->comment('回执单号');
			$table->integer('payment_id')->unsigned()->default(0)->comment('支付方式');
			$table->integer('lease_service_id')->unsigned()->default(0)->index()->comment('服务记录ID');
			$table->integer('service_id')->default(0)->index('service_id');
			$table->integer('agent_id')->default(0)->index('agent_id');
			$table->integer('province_id')->default(0)->index('province_id');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_payments` comment '租点支付信息表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_payments');
	}

}
