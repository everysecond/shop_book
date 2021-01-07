<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseEventFlowsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_event_flows', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('date', 10)->default('')->comment('日期');
			$table->integer('year')->default(0)->comment('年');
			$table->integer('month')->default(0)->comment('月');
			$table->integer('day')->default(0)->comment('天');
			$table->char('client_ip', 20)->default('')->comment('客户端ip');
			$table->boolean('app_type')->default(0)->comment('app类型');
			$table->string('page_url')->nullable()->comment('页面地址');
			$table->char('system_type', 20)->nullable()->default('')->comment('系统类型');
			$table->string('version', 32)->default('')->comment('APP版本号');
			$table->integer('hour')->default(0)->comment('时');
			$table->integer('user_id')->unsigned()->default(0)->comment('用户id');
			$table->integer('province_id')->unsigned()->default(0)->comment('省');
			$table->integer('times')->unsigned()->default(0)->comment('次数');
			$table->string('machine_id')->nullable()->comment('设备编号');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_event_flows` comment '用户活跃事件'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_event_flows');
	}

}
