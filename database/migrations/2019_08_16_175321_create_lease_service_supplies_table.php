<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaseServiceSuppliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_service_supplies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date')->index('date2')->comment('日期（例如 2019-8-12）');
            $table->integer('service_id')->default(0)->comment('服务点id');
            $table->integer('num')->default(0)->comment('数量');
            
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_service_supplies` comment '同步库存补货数量(无省份id)'");//表注释
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_service_supplies');
    }
}
