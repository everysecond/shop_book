<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseRentRebateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_rent_rebate', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('rent_release_date')->index('rent_release_date')->comment('日期');
			$table->integer('rent_release_num')->default(0)->comment('退租数');
			$table->integer('advance_rent_release')->default(0)->comment('提前退租');
			$table->integer('expire_rent_release_num')->default(0)->comment('到期当日退租数');
			$table->integer('overtime_ten_rent_release_num')->default(0)->comment('到期0-10天退租数');
			$table->integer('overtime_ten_thirty_rent_release_num')->default(0)->comment('到期10-30天退租数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable()->default(0);
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->decimal('rent_release_amount', 11)->default(0.00)->comment('退租金额');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_rent_rebate` comment '退租统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_rent_rebate');
	}

}
