<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmPlanRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_plan_records', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(1)->comment('类型 1记录 2计划');
			$table->bigInteger('cus_id')->unsigned()->default(0)->comment('客户id');
			$table->boolean('follow_mode')->default(0)->comment('跟进方式');
			$table->text('content', 65535)->nullable()->comment('跟进内容');
			$table->integer('contact_id')->unsigned()->default(0)->comment('联系人id');
			$table->string('follow_user_ids')->default('')->comment('跟进人员ids');
			$table->bigInteger('follow_at')->unsigned()->nullable()->comment('跟进时间');
			$table->bigInteger('created_by')->unsigned()->nullable();
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_plan_records` comment '客户跟踪记录计划表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_plan_records');
	}

}
