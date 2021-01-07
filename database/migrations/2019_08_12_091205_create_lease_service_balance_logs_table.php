<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceBalanceLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_balance_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('service_id')->unsigned()->default(0)->index('service_id')->comment('用户id');
			$table->boolean('source')->default(0)->comment('资金来源');
			$table->boolean('type')->default(0)->comment('类型 1余额增加 2余额减少');
			$table->decimal('amount', 10)->unsigned()->default(0.00)->comment('变动金额');
			$table->decimal('balance_before', 10)->default(0.00)->comment('余额');
			$table->dateTime('created_at')->nullable()->comment('创建时间');
			$table->integer('lease_service_id')->unsigned()->default(0);
			$table->string('remark')->default('');
			$table->string('relation_type')->nullable()->comment('关系类型');
			$table->integer('relation_id')->unsigned()->default(0)->index('service_balance_logs_relation_id_index')->comment('关系类型ID');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_balance_logs` comment '网点余额变动表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_balance_logs');
	}

}
