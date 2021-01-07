<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseInsuranceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_insurance', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('rent_date')->comment('日期');
			$table->integer('rent_num')->default(0)->comment('租赁数');
			$table->integer('insure_num')->default(0)->comment('投保数');
			$table->integer('uninsured_num')->default(0)->comment('未投保数');
			$table->integer('report_loss_num')->default(0)->comment('报失数');
			$table->integer('report_loss_battery_num')->default(0)->comment('电池报失总额');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable();
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->integer('renewal_num')->default(0)->comment('续租数');
			$table->integer('renewal_insure_num')->default(0)->comment('续租投保数');
			$table->integer('rent_insure_num')->default(0)->comment('租赁投保数');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_insurance` comment '投保报失统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_insurance');
	}

}
