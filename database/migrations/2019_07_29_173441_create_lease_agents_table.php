<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseAgentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_agents', function(Blueprint $table)
		{
			$table->increments('id')->comment('ID');
			$table->integer('pid')->unsigned()->comment('上级');
			$table->string('name', 50)->comment('名称');
			$table->boolean('deep')->default(0)->comment('深度');
			$table->boolean('type')->default(1)->comment('区域类型');
			$table->string('remark')->comment('备注');
			$table->boolean('sort')->default(0)->comment('排序');
			$table->decimal('reserve_fund', 10)->unsigned()->default(0.00)->comment('备用金');
			$table->timestamps();
			$table->softDeletes()->comment('删除时间');
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_agents` comment '租点区域表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_agents');
	}

}
