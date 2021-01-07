<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLeaseContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("lease_contracts", function (Blueprint $table) {
            $table->bigInteger('order_scan_time')->unsigned()->nullable(false)->default(0)->comment('订单扫码时间');
            $table->bigInteger('battery_scan_time')->unsigned()->nullable(false)->default(0)->comment('电池扫码时间');
            $table->bigInteger('install_payed_time')->unsigned()->nullable(false)->default(0)->comment('安装完成时间');
            $table->bigInteger('install_sure_time')->unsigned()->nullable(false)->default(0)->comment('安装确认时间');
            $table->string('install_payed_date', 64)->nullable()->comment('安装完成日期');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
