<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServiceStockLogInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_service_stock_log_infos', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('date', 10)->nullable()->comment('记录日期');
			$table->integer('service_id')->unsigned()->default(0)->index('service_id')->comment('仓库');
			$table->integer('model_id')->unsigned()->default(0)->index('model_id')->comment('电池model_id');
			$table->integer('lease_type')->unsigned()->default(0)->comment('数据ID');
			$table->integer('sku_before')->default(0)->comment('之前库存');
			$table->integer('num')->default(0)->comment('数量');
			$table->integer('sku_after')->default(0)->comment('之后库存');
			$table->boolean('battery_type')->default(0)->index('battery_type')->comment('电池类型');
			$table->boolean('stock_type')->default(1)->comment('0初始化 1 进货 2出货 ');
			$table->string('relation_type', 100)->default('')->comment('关联类型');
			$table->integer('relation_id')->default(0);
			$table->string('remark', 50)->nullable()->default('');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('agent_id')->unsigned()->default(0)->index()->comment('区域id');
			$table->integer('province_id')->unsigned()->default(0)->comment('区域id');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_stock_log_infos` comment '网点库存日志'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_service_stock_log_infos');
	}

}
