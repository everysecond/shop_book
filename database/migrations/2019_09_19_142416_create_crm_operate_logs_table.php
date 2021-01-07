<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmOperateLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_operate_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('table_name', 50)->default('')->comment('表名');
			$table->integer('resource_id')->comment('资源id');
			$table->string('type', 50)->default('')->comment('记录类型');
			$table->text('content', 65535)->comment('记录内容');
			$table->integer('target_user_id')->unsigned()->default(0)->comment('目标id');
			$table->integer('created_by')->unsigned()->comment('操作人');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_operate_logs` comment '客户相关操作记录表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_operate_logs');
	}

}
