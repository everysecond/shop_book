<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaseServiceStockLogsAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_service_stock_logs_agents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index('date')->comment('日期（例如 2019-8-12）');
            $table->integer('agent_id')->default(0)->comment('区域id');
            $table->integer('service_id')->default(0)->comment('服务点id');
            $table->integer('battery_type')->default(0)->comment('电池类型（1,2,3）');
            $table->integer('num')->default(0)->comment('数量');
            
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_stock_logs_agents` comment '同步库存数量(无省份id)'");//表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_service_stock_logs_agents');
    }
}
