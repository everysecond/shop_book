<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseProcessRegisterPeriodsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_process_register_periods', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->date('register_date')->index('renewal_date')->comment('注册日期');
			$table->integer('register_num')->default(0)->comment('当日注册数');
			$table->integer('today_num')->default(0)->comment('当天发起租赁');
			$table->integer('one_three_num')->default(0)->comment('1-3天发起租赁');
			$table->integer('four_seven_num')->default(0)->comment('4到7天发起租赁');
			$table->integer('eight_ten_num')->default(0)->comment('8到10天发起租赁');
			$table->integer('eleven_thirty_num')->default(0)->comment('11到30天发起租赁');
			$table->integer('thirty_no_num')->default(0)->comment('30天内未发起租赁');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->bigInteger('created_at')->nullable();
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_process_register_periods` comment '注册租赁周期'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_process_register_periods');
	}

}
