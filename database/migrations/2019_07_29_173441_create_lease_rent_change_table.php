<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseRentChangeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_rent_change', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('register_num')->default(0)->comment('注册数');
			$table->integer('rent_num')->default(0)->comment('租赁数');
			$table->integer('sign_in_num')->default(0)->comment('登录数');
			$table->integer('rent_change_num')->default(0)->comment('换租数');
			$table->date('rent_change_date')->index('rent_change_date')->comment('换租时间');
			$table->bigInteger('created_at')->nullable()->default(0);
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->integer('province_id')->default(0)->index('province_id')->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('县');
			$table->boolean('type')->default(1)->comment('统计类型：1：当天/每天 2：累计');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_rent_change` comment '换租统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_rent_change');
	}

}
