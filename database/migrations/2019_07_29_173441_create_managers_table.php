<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('managers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('mobile', 11)->default('')->unique('mobile_unique')->comment('手机号');
			$table->string('name', 20)->default('')->comment('姓名');
			$table->string('username', 20)->default('')->unique('username_unique')->comment('账号');
			$table->string('password', 100)->default('')->comment('密码');
			$table->string('remember_token', 100)->default('')->comment('记住密码');
			$table->boolean('status')->default(0)->comment('管理员状态：1正常，2冻结');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('managers');
	}

}
