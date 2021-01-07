<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceRetiresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_retires', function(Blueprint $table)
		{
			$table->increments('id');
			$table->decimal('deposit', 10)->unsigned()->default(0.00)->comment('退押金');
			$table->decimal('rental', 10)->default(0.00)->comment('退租金');
			$table->boolean('status')->default(0)->comment('状态 1:待审核 2:待退款 3:已完成 4:已取消');
			$table->dateTime('confirmed_at')->nullable()->comment('确认时间');
			$table->decimal('amount', 10)->unsigned()->default(0.00)->comment('退款金额');
			$table->boolean('direction')->default(0)->comment('1：原路退回，2：余额');
			$table->string('payment_no', 60)->nullable()->default('')->comment('回执单号');
			$table->dateTime('refunded_at')->nullable()->comment('退款时间');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->nullable()->default(0)->comment('区县');
			$table->integer('service_id')->unsigned()->default(0)->comment('租赁的服务点');
			$table->integer('agent_id')->default(0);
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_retires` comment '退租服务表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_retires');
	}

}
