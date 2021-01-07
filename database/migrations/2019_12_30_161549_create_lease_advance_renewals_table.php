<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseAdvanceRenewalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_advance_renewals', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->date('renewal_date')->comment('日期');
			$table->integer('advance_one_five_renewal_num')->default(0)->comment('续租1到3天');
			$table->integer('advance_six_ten_renewal_num')->default(0)->comment('4到7天续租户数');
			$table->integer('advance_ten_thirty_renewal_num')->default(0)->comment('8到10天续租数');
			$table->integer('advance_over_thirty_renewal_num')->default(0)->comment('到期11-30天续租数');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->nullable()->default(0)->comment('区县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
			$table->bigInteger('created_at')->nullable();
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_advance_renewals` comment '提前续租数统计'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_advance_renewals');
	}

}
