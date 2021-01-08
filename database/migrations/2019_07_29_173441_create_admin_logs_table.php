<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAdminLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('admin_logs', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('name')->comment('用户姓名');
			$table->string('roles')->comment('用户所属组');
			$table->string('content')->comment('操作内容');
			$table->dateTime('create_date')->index('create_date')->comment('操作时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `admin_logs` comment '操作日志'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('admin_logs');
	}

}
