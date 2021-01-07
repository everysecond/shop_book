<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_users', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('user_id')->comment('租点用户Id');
			$table->string('mobile', 13)->default('')->index('mobile')->comment('用户名(手机号)');
			$table->string('mobile_model')->default('')->comment('手机型号');
			$table->string('nickname')->default('')->comment('显示昵称');
			$table->integer('age')->unsigned()->default(0)->comment('年龄');
			$table->date('birthday')->nullable()->comment('出生日期');
			$table->boolean('sex')->nullable()->comment('性别（1：男 2：女）');
			$table->boolean('register_type')->default(1)->comment('注册类型 1手机号 2微信 3qq 4 支付宝');
			$table->string('register_from')->default('0')->index('register_from')->comment('注册来源');
			$table->integer('province_id')->default(0)->comment('省');
			$table->integer('city_id')->default(0)->comment('市');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->string('register_at', 64)->comment('注册日期');
			$table->boolean('register_hour')->default(0)->comment('注册时段(0-24点)');
			$table->bigInteger('created_at')->unsigned()->default(0)->comment('创建时间');
			$table->bigInteger('updated_at')->unsigned()->nullable()->comment('最后更新时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_users` comment '租点用户信息表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_users');
	}

}
