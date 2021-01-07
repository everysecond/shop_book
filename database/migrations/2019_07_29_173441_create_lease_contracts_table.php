<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseContractsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_contracts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->default(0)->index('user_id')->comment('用户ID');
			$table->string('user_mobile', 13)->default('')->comment('用户名(手机号)');
			$table->string('user_nickname')->default('')->comment('显示昵称');
			$table->string('user_register_at', 64)->default('')->comment('用户注册日期');
			$table->string('contract_no', 20)->default('')->unique('contract_no')->comment('合同号');
			$table->integer('model_id')->unsigned()->default(0)->comment('型号ID');
			$table->string('model_name', 20)->default('')->comment('电池型号');
			$table->string('single_model', 20)->default('')->comment('单只型号');
			$table->boolean('single_num')->default(0)->comment('单只数量');
			$table->decimal('deposit', 10)->unsigned()->default(0.00)->comment('押金');
			$table->boolean('status')->default(0)->comment('状态，1:未生效 2:待生效 3:已生效 4:已退租 5:已丢失 6:已取消,7已续约 8已换租');
			$table->integer('lease_service_id')->unsigned()->default(0)->comment('当前操作');
			$table->boolean('contract_term')->default(1)->comment('合约期');
			$table->string('contract_unit', 10)->default('year')->comment('合约期单位');
			$table->boolean('lease_term')->default(1)->comment('租期，以期计算');
			$table->string('lease_unit', 10)->default('')->comment('每期单位');
			$table->boolean('term_index')->default(0)->comment('当前第几期');
			$table->string('created_date', 64)->nullable()->comment('创建日期');
			$table->timestamps();
			$table->dateTime('effected_at')->nullable()->comment('合约生效时间');
			$table->dateTime('contract_expired_at')->nullable()->comment('合约到期时间');
			$table->dateTime('lease_expired_at')->nullable()->comment('租金到期时间');
			$table->dateTime('retired_at')->nullable()->comment('退租时间');
			$table->integer('prev_id')->unsigned()->default(0)->comment('上一级合同号ID');
			$table->integer('root_id')->unsigned()->default(0)->comment('根合同号ID');
			$table->text('rentals')->nullable()->comment('租金，二年[300.00,210.00]');
			$table->decimal('rental_all', 10)->default(0.00)->comment('总租金');
			$table->bigInteger('group_code')->unsigned()->default(0)->comment('电池组');
			$table->integer('service_id')->unsigned()->default(0)->comment('租赁的服务点');
			$table->string('service_name')->default('')->comment('服务点名称');
			$table->char('service_mobile', 13)->default('')->comment('联系方式');
			$table->string('service_owner_name')->default('')->comment('户主名称');
			$table->integer('service_agent_id')->default(0)->comment('区域id');
			$table->integer('service_province_id')->unsigned()->default(0)->comment('省');
			$table->string('service_province_name')->nullable()->default('')->comment('省名称');
			$table->integer('service_city_id')->unsigned()->default(0)->comment('市');
			$table->string('service_city_name')->nullable()->default('')->comment('市名称');
			$table->integer('service_county_id')->default(0)->comment('区县');
			$table->string('service_county_name')->nullable()->default('')->comment('区县名称');
			$table->bigInteger('service_town_id')->unsigned()->default(0)->comment('城镇');
			$table->string('service_town_name')->nullable()->default('')->comment('区县名称');
			$table->string('service_address')->default('')->comment('地址');
			$table->integer('service_business_id')->unsigned()->default(0)->comment('业务员id');
			$table->boolean('payment_type')->default(0)->comment('支付类型');
			$table->boolean('payment_status')->default(0)->comment('支付状态');
			$table->dateTime('payment_payed_at')->nullable()->comment('支付时间');
			$table->decimal('payment_amount', 10)->unsigned()->default(0.00)->comment('实付金额');
			$table->string('recycle_model', 20)->default('')->comment('回收的电池型号');
			$table->integer('recycle_model_id')->unsigned()->default(0)->comment('回收电池型号');
			$table->decimal('recycle_price', 10)->unsigned()->default(0.00)->comment('回收的价格');
			$table->string('remark')->nullable()->comment('备注');
			$table->text('service_reward')->nullable()->comment('网点奖励和分红');
			$table->decimal('prepayment', 10)->unsigned()->default(0.00)->comment('预付款');
			$table->decimal('pre_balance', 10)->unsigned()->default(0.00)->comment('预付款余额');
			$table->integer('agent_id')->default(0)->index('agent_id');
			$table->integer('province_id')->default(0)->index('province_id');
			$table->index(['lease_expired_at','status'], 'lease_expired_at');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_contracts` comment '租点合约表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_contracts');
	}

}
