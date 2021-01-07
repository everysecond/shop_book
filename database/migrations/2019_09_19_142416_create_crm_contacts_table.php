<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_contacts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('cus_id')->unsigned()->default(0)->comment('客户id');
			$table->char('mobile', 11)->default('')->comment('手机号');
			$table->string('name', 20)->default('')->comment('姓名');
			$table->string('wechat', 100)->default('')->comment('微信');
			$table->string('position', 100)->default('')->comment('职位');
			$table->boolean('is_key')->default(2)->comment('是否关键决策人 1是 2不是');
			$table->string('sex', 100)->default('未知')->comment('称呼(性别) 未知 先生 女士');
			$table->integer('agent_id')->default(0)->comment('区域id');
			$table->integer('province_id')->unsigned()->default(0)->comment('省');
			$table->string('address')->default('')->comment('地址');
			$table->text('memo', 65535);
			$table->bigInteger('created_by')->unsigned()->nullable();
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_contacts` comment '客户联系人表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_contacts');
	}

}
