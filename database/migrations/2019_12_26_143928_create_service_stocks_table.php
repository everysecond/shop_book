<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateServiceStocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('service_stocks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('service_id')->unsigned()->default(0)->index('service_id')->comment('服务点ID');
			$table->integer('model_id')->unsigned()->default(0)->index('model_id')->comment('电池型号');
			$table->string('model_name', 30)->comment('电池名称');
			$table->boolean('lease_type')->default(0)->index('lease_type')->comment('租赁电池类型 ');
			$table->integer('sku')->unsigned()->default(0)->comment('库存数量');
			$table->boolean('battery_type')->default(0)->index('battery_type')->comment('电池类型 1租赁 2回收');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('agent_id')->unsigned()->default(0)->index('bl_service_stocks_agent_id_index')->comment('区域id');
			$table->integer('county_id')->unsigned()->nullable()->comment('区/县id');
			$table->integer('city_id')->unsigned()->nullable()->comment('市id');
			$table->integer('province_id')->unsigned()->nullable()->comment('省id');
			$table->unique(['service_id','model_id','lease_type','battery_type'], 'service_id,model_id,lease_type,battery_type');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `service_stocks` comment '网点库存表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('service_stocks');
	}

}
