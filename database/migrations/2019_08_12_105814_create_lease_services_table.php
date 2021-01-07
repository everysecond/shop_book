<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaseServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lease_services', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('created_date', 10)->nullable()->default('')->comment('创建日期');
			$table->char('mobile', 13)->default('')->unique('mobile')->comment('联系方式');
			$table->string('service_name')->default('')->comment('服务点名称');
			$table->string('owner_name')->default('')->comment('户主名称');
			$table->integer('agent_id')->default(0)->comment('区域id');
			$table->integer('province_id')->unsigned()->default(0)->comment('省');
			$table->string('province_name')->nullable()->default('')->comment('省名称');
			$table->integer('city_id')->unsigned()->default(0)->comment('市');
			$table->string('city_name')->nullable()->default('')->comment('市名称');
			$table->integer('county_id')->default(0)->comment('区县');
			$table->string('county_name')->nullable()->default('')->comment('区县名称');
			$table->bigInteger('town_id')->unsigned()->default(0)->comment('城镇');
			$table->string('town_name')->nullable()->default('')->comment('区县名称');
			$table->string('address')->default('')->comment('地址');
			$table->string('avatar')->nullable()->default('')->comment('本人正面头像');
			$table->string('idcard', 50)->default('')->comment('身份证号');
			$table->integer('age')->unsigned()->default(0)->comment('年龄');
			$table->boolean('sex')->nullable()->default(0)->comment('性别（1：男 2：女）');
			$table->string('idcard_front', 500)->default('')->comment('身份证前面');
			$table->string('idcard_back', 500)->default('')->comment('身份证后面');
			$table->text('photos')->nullable()->comment('门头照片');
			$table->geometry('location')->nullable()->comment('地理位置');
			$table->char('location_hash', 128)->nullable()->index('location_has')->comment('位置哈希');
			$table->decimal('freezing_balance', 10)->nullable()->default(0.00);
			$table->decimal('balance', 10)->nullable()->default(0.00)->comment('余额');
			$table->boolean('status')->default(3)->comment('状态（1：正常 0：已冻结 2:审核拒绝  3:审核中）');
			$table->decimal('bail', 12)->unsigned()->nullable()->default(0.00)->comment('保证金');
			$table->boolean('vip')->default(0)->comment('是否是vip（1：是 0：否）');
			$table->decimal('score', 2, 1)->unsigned()->default(5.0)->comment('综合评分 0.0~5.0');
			$table->timestamps();
			$table->dateTime('experied_at')->nullable()->comment('过期时间');
			$table->dateTime('audited_at')->nullable()->comment('审核时间');
			$table->softDeletes()->comment('删除时间');
			$table->decimal('lease_limit', 10)->default(0.00)->comment('最大库存额度');
			$table->integer('lease_sku')->unsigned()->default(0)->comment('租赁库存');
			$table->integer('recycle_sku')->unsigned()->default(0)->comment('回收库存');
			$table->integer('back_sku')->unsigned()->default(0)->comment('退回库存');
			$table->text('protocol')->nullable()->comment('协议照片');
			$table->integer('business_id')->unsigned()->default(0)->comment('业务员id');
			$table->dateTime('constract_begin_at')->nullable()->comment('合同开始时间');
			$table->dateTime('constract_end_at')->nullable()->comment('合同结束时间');
			$table->string('remark')->nullable()->comment('备注');
			$table->string('market_person', 100)->nullable()->comment('营销人');
			$table->decimal('league', 10)->default(0.00)->comment('加盟费');
		});

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_services` comment '租点网点表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lease_services');
	}

}
