<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateKdUserBalanceLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kd_user_balance_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date', 10)->nullable()->comment('统计的日期');
            $table->text('json', 65535)->nullable()->comment('各站点收益json串');
            $table->integer('total')->unsigned()->default(0)->comment('当日总余额');
        });
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_app_down_logs` comment '终端下载统计日志表'");//表注释
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `kd_user_balance_logs` comment '快点商家余额变动统计日志表'");//表注释
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('kd_user_balance_logs');
    }

}
