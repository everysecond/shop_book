<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceRetrievesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_retrieves', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->date('date')->index('date')->comment('日期（例如 2019-8-12）');
			$table->integer('service_id')->default(0)->comment('服务点id');
			$table->integer('num')->default(0)->comment('数量');
			$table->boolean('status')->default(0)->comment('状态 2为退货 3为回收');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_supplies` comment '同步库存补货数量(无省份id)'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_retrieves');
	}

}
