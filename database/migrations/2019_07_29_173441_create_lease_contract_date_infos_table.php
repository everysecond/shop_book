<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseContractDateInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_contract_date_infos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(0)->comment('记录类型,1为新用户 2为老用户');
			$table->string('date', 64)->default('')->comment('统计的日期');
			$table->integer('today_num')->unsigned()->default(0)->comment('当日租赁数');
			$table->integer('total_num')->unsigned()->default(0)->comment('累积到当日总租赁数');
			$table->decimal('today_rental', 10)->unsigned()->default(0.00)->comment('当日租赁金额');
			$table->decimal('total_rental', 12)->unsigned()->default(0.00)->comment('累积到当日总租赁金额');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->bigInteger('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->bigInteger('updated_at')->unsigned()->default(0)->comment('更新时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_contract_date_infos` comment '租点合约统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_contract_date_infos');
	}

}
