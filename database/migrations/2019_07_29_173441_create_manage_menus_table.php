<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateManageMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('manage_menus', function(Blueprint $table)
		{
			$table->integer('id', true)->comment('ID');
			$table->integer('pid')->default(0)->comment('父ID');
			$table->string('name', 50)->default('')->comment('名称');
			$table->string('route')->default('')->comment('链接地址');
			$table->integer('sort')->default(0)->comment('排序');
			$table->string('icon', 200)->default('')->comment('样式');
			$table->boolean('status')->default(1)->comment('状态(1 显示 0 隐藏)');
			$table->boolean('level')->default(1)->comment('层级');
            $table->string('terminal')->nullable(false)->default('web')->comment('菜单所属终端');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `manage_menus` comment '菜单管理表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('manage_menus');
	}

}
