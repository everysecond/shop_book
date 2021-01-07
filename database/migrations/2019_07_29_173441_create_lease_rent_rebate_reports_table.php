<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseRentRebateReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_rent_rebate_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('rent_release_date')->comment('日期');
			$table->integer('rent_release_num')->default(0)->comment('退租数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable()->default(0);
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->integer('overtime_one_three_rent_future_num')->default(0)->comment('未来1到3天发起租赁');
			$table->integer('expire_rent_future_num')->default(0)->comment('到期当日发起租赁数');
			$table->integer('overtime_four_seven_rent_future_num')->default(0)->comment('未来4到7天发起租赁数');
			$table->integer('overtime_eight_ten_rent_future_num')->default(0)->comment('以后8到10天发起起租赁数');
			$table->integer('overtime_ten_thirty_rent_future_num')->default(0)->comment('未来到期10-30天发起租赁数');
			$table->integer('overtime_thirty_no_rent_future_num')->default(0)->comment('未来到期三十天未发起租赁');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_rent_rebate_reports` comment '退租报表统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_rent_rebate_reports');
	}

}
