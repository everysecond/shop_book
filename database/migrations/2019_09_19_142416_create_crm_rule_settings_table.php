<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmRuleSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_rule_settings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->boolean('type')->default(0)->comment('规则用户来源：1：车主用户-租点；2：网点用户-租点');
			$table->text('json', 65535)->nullable()->comment('规则字符串：inflow_rules：流入规则是否启用
unleased：未租赁用户流入选项
lease_expires：到期租赁流入选项
international_waters_1：流入公海
no_track：未跟踪返还选项
return_rules：返还规则是否启用
no_rent：未租赁返还选项
international_waters_2：返回公海');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_rule_settings` comment '公海定时流入流出规则表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_rule_settings');
	}

}
