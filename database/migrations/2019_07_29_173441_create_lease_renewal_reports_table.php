<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseRenewalReportsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_renewal_reports', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('renewal_date')->comment('日期');
			$table->integer('expire_renewal_num')->default(0)->comment('到期当日续租数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable();
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->integer('expire_rent_num')->default(0)->comment('当日到期数');
			$table->integer('overtime_one_three_renewal_future_num')->default(0)->comment('未来1到3天发起续租');
			$table->integer('overtime_four_seven_renewal_future_num')->default(0)->comment('未来4到7天续租数');
			$table->integer('overtime_eight_ten_renewal_future_num')->default(0)->comment('以后8到10天续租数');
			$table->integer('overtime_ten_thirty_renewal_future_num')->default(0)->comment('未来到期10-30天续租数');
			$table->integer('overtime_thirty_no_renewal_future_num')->default(0)->comment('未来到期三十天未发起续租');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_renewal_reports` comment '续租报表统计'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_renewal_reports');
	}

}
