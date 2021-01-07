<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceWithdrawsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_withdraws', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('manager_id')->unsigned()->nullable()->default(0)->comment('操作人ID');
			$table->integer('service_id')->unsigned()->default(0)->index('index_user_id')->comment('用户ID');
			$table->decimal('amount', 10)->default(0.00)->comment('提现金额');
			$table->boolean('status')->default(0)->index('index_status')->comment('状态 0 审核中 1 审核通过 2审核不通过');
			$table->string('reply')->default('')->comment('回复内容');
			$table->string('remark')->default('')->comment('操作员备注（用户不可见）');
			$table->string('message')->nullable()->comment('用户留言（要求）');
			$table->string('card_number', 50)->default('')->comment('卡号');
			$table->string('bank')->default('')->index('bank')->comment('银行类型');
			$table->string('name', 50)->default('')->comment('银行卡户名（真实姓名）');
			$table->string('province', 50)->default('');
			$table->string('city', 50)->default('');
			$table->integer('transfer_type')->default(0)->comment('1 私卡 2 公卡');
			$table->integer('is_auto')->default(0);
			$table->integer('auto_status')->default(0)->comment('1等待支付 10 支付成功 20等待查询 40 支付失败');
			$table->integer('cas_vcode')->default(0);
			$table->timestamps();
			$table->softDeletes();
			$table->integer('agent_id')->unsigned()->default(0)->index('bl_service_withdraws_agent_id_index')->comment('区域id');
			$table->dateTime('arrival_at')->nullable()->comment('到账时间');
			$table->integer('province_id')->unsigned()->default(0)->comment('省id');
		});
        Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_withdraws` comment '提现申请表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_withdraws');
	}

}
