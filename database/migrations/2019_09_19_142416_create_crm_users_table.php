<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_users', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->bigInteger('user_id')->unsigned()->default(0)->index('user_id')->comment('用户(或网点)在源系统id');
			$table->boolean('is_top')->default(0)->comment('是否置顶 默认0未置顶 1已置顶');
			$table->boolean('is_mark')->default(0)->comment('是否标记 默认0未标记 1已标记');
			$table->string('mobile', 20)->default('')->index('mobile')->comment('用户名(手机号)');
			$table->string('name')->default('')->comment('客户全称');
			$table->string('owner_name')->default('')->comment('户主名称');
			$table->string('short_name')->default('')->comment('客户简称(助记名称)');
			$table->integer('sea_type')->unsigned()->default(0)->comment('公海类型');
			$table->boolean('cus_type')->default(0)->comment('客户类型（1：租点车主 2：租点网点 3：快点车主 4：快点网点）');
			$table->boolean('cus_level')->default(0)->comment('客户等级（1：重点客户 2：普通客户 3：非优先客户）');
			$table->boolean('cus_source')->default(0)->comment('客户来源（1：crm录入 2：租点系统 3：中台录入）');
			$table->boolean('history_deal')->default(0)->comment('历史成交（1：未成交 2：已成交）');
			$table->boolean('sex')->nullable()->comment('性别（1：男 2：女）');
			$table->boolean('status')->default(1)->comment('用户状态（1：正常 0：已冻结）');
			$table->string('charger_name', 40)->default('')->comment('负责人姓名');
			$table->integer('charger_id')->unsigned()->default(0)->comment('负责人id');
			$table->string('pre_charger_name', 40)->default('')->comment('前负责人姓名');
			$table->integer('pre_charger_id')->unsigned()->default(0)->comment('前负责人id');
			$table->integer('business_id')->unsigned()->default(0)->comment('业务员id');
			$table->string('business_name', 40)->default('')->comment('业务员名称');
			$table->integer('agent_id')->default(0)->index('agent_id')->comment('区域id');
			$table->integer('province_id')->unsigned()->default(0)->index('province_id')->comment('省');
			$table->string('province_name')->nullable()->default('')->comment('省名称');
			$table->integer('city_id')->unsigned()->default(0)->comment('市');
			$table->string('city_name')->nullable()->default('')->comment('市名称');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->string('county_name')->nullable()->default('')->comment('区县名称');
			$table->bigInteger('town_id')->unsigned()->default(0)->comment('城镇');
			$table->string('town_name')->nullable()->default('')->comment('区县名称');
			$table->string('address')->default('')->comment('地址');
			$table->string('area')->nullable()->default('')->comment('所在地区');
			$table->text('memo', 65535)->nullable()->comment('备注');
			$table->dateTime('constract_begin_at')->nullable()->comment('合同开始时间');
			$table->dateTime('constract_end_at')->nullable()->comment('合同结束时间');
			$table->decimal('deposit', 10)->default(0.00)->comment('用户押金');
			$table->decimal('freezing_balance', 10)->default(0.00)->comment('冻结余额');
			$table->decimal('balance', 10)->default(0.00)->comment('用户余额 自动续租时可为负数');
			$table->decimal('prepayment', 10)->nullable()->default(0.00)->comment('预付款租金');
			$table->boolean('is_auth')->default(0)->comment('是否实名认证 0 未认证 1已认证 2审核中');
			$table->integer('created_by')->unsigned()->default(0)->comment('创建人id（cps中的id）');
			$table->timestamps();
			$table->softDeletes();
			$table->date('birthday')->nullable();
			$table->decimal('bail', 12)->unsigned()->nullable()->default(0.00)->comment('保证金');
			$table->decimal('league', 10)->unsigned()->default(0.00)->comment('加盟费');
			$table->bigInteger('allotted_time')->unsigned()->nullable()->default(0)->comment('分配时间');
			$table->bigInteger('inflow_time')->unsigned()->nullable()->default(0)->comment('流入时间');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_users` comment 'crm客户表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_users');
	}

}
