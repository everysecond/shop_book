<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseChangeReasonTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_change_reason', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(1)->comment('换租原因：1：漏液 2：跑不远 3：鼓包');
			$table->integer('change_reason_num')->default(0)->comment('换组原因数');
			$table->date('change_reason_date')->index('change_reason_date')->comment('换租时间');
			$table->bigInteger('created_at')->nullable()->default(0);
			$table->bigInteger('updated_at')->nullable();
			$table->bigInteger('deleted_at')->nullable();
			$table->integer('province_id')->default(0)->index('province_id')->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('县');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_change_reason` comment '换租原因统计表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_change_reason');
	}

}
