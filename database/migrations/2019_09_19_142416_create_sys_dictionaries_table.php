<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSysDictionariesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sys_dictionaries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('dict_type', 100)->default('')->comment('字典类型code');
			$table->string('type_means', 100)->default('')->comment('字典解释');
			$table->string('code', 100)->default('')->comment('字典键值');
			$table->string('means', 100)->default('')->comment('字典示意');
			$table->integer('sort')->default(0)->comment('排序');
			$table->string('memo')->default('')->comment('备注');
			$table->bigInteger('created_by')->unsigned()->nullable();
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
			$table->bigInteger('deleted_at')->unsigned()->nullable();
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sys_dictionaries` comment '数据字典表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sys_dictionaries');
	}

}
