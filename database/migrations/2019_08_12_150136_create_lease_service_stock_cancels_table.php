<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaseServiceStockCancelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_service_stock_cancels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index('date')->comment('日期（例如 2019-8-12）');
            $table->tinyInteger('systemtype')->default(1)->index()->comment('网点库存类型 1为补货 2为退货 3为回收');
            $table->tinyInteger('type')->default(1)->index()->comment('类型 ：1为退货数量 2为退货申请数');
            $table->integer('total_num')->default(0)->comment('总数量');
            $table->integer('num_65')->default(0)->comment('湖南数量');
            $table->integer('num_78')->default(0)->comment('安徽数量');
            $table->integer('num_85')->default(0)->comment('江西数量');
            $table->integer('num_91')->default(0)->comment('河南数量');
            $table->integer('num_110')->default(0)->comment('广西数量');
            $table->integer('num_118')->default(0)->comment('江苏数量');
            $table->integer('num_129')->default(0)->comment('湖北数量');
            $table->integer('num_132')->default(0)->comment('福建数量');
            $table->integer('num_145')->default(0)->comment('浙江数量');
            $table->integer('num_209')->default(0)->comment('台湾数量');
            $table->integer('num_214')->default(0)->comment('山东数量');
            
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_stock_cancels` comment '网点补货/退货/回收数量与申请数表'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lease_service_stock_cancels');
    }
}
