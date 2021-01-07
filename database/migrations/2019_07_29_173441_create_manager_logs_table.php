<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManagerLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manager_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('route', 50)->default('')->comment('路由名称');
			$table->string('name', 50)->default('')->comment('菜单名称');
			$table->integer('manager_id')->unsigned()->default(0)->comment('管理员ID');
			$table->string('manager_name', 50)->default('')->comment('用户所属组');
			$table->string('method', 20)->default('')->comment('请求类型');
			$table->string('url', 1024)->default('')->comment('请求地址');
			$table->string('ip', 20)->default('')->comment('IP');
			$table->text('data', 65535)->comment('数据');
			$table->bigInteger('created_at')->unsigned()->index('created_at')->comment('操作时间');
			$table->bigInteger('updated_at')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manager_logs');
	}

}
