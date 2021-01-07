<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseLogisticsStockLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_logistics_stock_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('logistics_id')->unsigned()->default(0)->index('service_id')->comment('物流');
			$table->integer('model_id')->unsigned()->default(0)->index('model_id')->comment('电池model_id');
			$table->string('model_name', 100)->default('')->comment('电池名称');
			$table->integer('lease_type')->unsigned()->default(0)->comment('数据ID');
			$table->integer('num')->default(0)->comment('数量');
			$table->decimal('weight', 10)->default(0.00)->comment('重量');
			$table->decimal('price', 10)->default(0.00)->comment('价格');
			$table->boolean('battery_type')->default(0)->index('battery_type')->comment('电池类型');
			$table->boolean('stock_type')->default(1)->comment('0初始化 1 进货 2出货 ');
			$table->boolean('type')->default(0)->comment('类型');
			$table->string('relation_type', 100)->default('')->index('relation_type')->comment('关联类型');
			$table->integer('relation_id')->default(0);
            $table->boolean('log_type')->default(0)->index('log_type')->comment('日志类型');
            $table->string('log_type_txt', 100)->default('新租抵押旧电池')->comment('日志类型名称');
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('创建时间');
			$table->integer('agent_id')->unsigned()->default(0)->index()->comment('顶级区域id');
			$table->integer('province_id')->unsigned()->default(0)->comment('省id');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_logistics_stock_logs` comment '租点物流库存日志'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_logistics_stock_logs');
	}

}
