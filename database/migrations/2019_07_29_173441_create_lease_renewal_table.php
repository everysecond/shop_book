<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseRenewalTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_renewal', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->date('renewal_date')->index('renewal_date')->comment('日期');
			$table->decimal('renewal_amount', 11)->default(0.00)->comment('续租总金额');
			$table->integer('renewal_user_num')->default(0)->comment('续租用户数');
			$table->integer('renewal_num')->default(0)->comment('续租数');
			$table->integer('advance_renewal')->default(0)->comment('提前续租数');
			$table->integer('expire_renewal_num')->default(0)->comment('到期当日续租数');
			$table->integer('overtime_one_three_renewal_num')->default(0)->comment('续租1到3天');
			$table->integer('overtime_four_seven_renewal_num')->default(0)->comment('4到7天续租户数');
			$table->integer('overtime_eight_ten_renewal_num')->default(0)->comment('8到10天续租数');
			$table->integer('overtime_ten_renewal_num')->default(0)->comment('到期0-10天续租数');
			$table->integer('overtime_ten_thirty_renewal_num')->default(0)->comment('到期11-30天续租数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->nullable()->default(0)->comment('区县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable();
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->integer('renewal_month_total')->default(0)->comment('续租周期总月份');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_renewal` comment '续租统计'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_renewal');
	}

}
