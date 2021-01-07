<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceExchangesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_exchanges', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('new_contract_id')->unsigned()->default(0)->comment('新合约 ID');
			$table->boolean('reason')->default(0)->comment('换租原因 1：跑不远 2：漏液 3：鼓包');
			$table->string('remark', 200)->default('')->comment('问题描述');
			$table->text('images')->nullable()->comment('图片');
			$table->integer('model_id')->unsigned()->default(0)->comment('新电池型号ID');
			$table->string('model_name', 20)->default('')->comment('电池型号名称');
			$table->boolean('is_different_model')->default(0)->comment('是否不同型号');
			$table->boolean('battery_type')->default(0)->comment('电池类型');
			$table->boolean('status')->default(0)->comment('状态 1待确认 2已确认 3待支付 4已完成 5已取消');
			$table->dateTime('confirmed_at')->nullable()->comment('确认时间');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->nullable()->default(0)->comment('区县');
			$table->integer('service_id')->unsigned()->default(0)->comment('租赁的服务点');
			$table->integer('agent_id')->default(0);
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_exchanges` comment '换租服务表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_exchanges');
	}

}
