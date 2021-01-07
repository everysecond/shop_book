<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaseCusEventLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lease_cus_event_logs', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('date', 10)->default('')->comment('统计的日期');
            $table->string('day', 10)->default('')->comment('统计的日期');
            $table->string('page_url',255)->nullable()->comment('事件地址');
            $table->string('url_name',255)->nullable(false)->default('')->comment('路由名称');
            $table->integer('times')->unsigned()->default(0)->comment('事件次数');
            $table->integer('user_num')->unsigned()->comment('事件人数');
            $table->integer('province_id')->unsigned()->default(0)->comment('省');
            $table->integer('city_id')->unsigned()->default(0)->comment('市');
            $table->integer('county_id')->unsigned()->default(0)->comment('区县');
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lease_cus_event_logs` comment 'C端用户活跃事件日志表'");//表注释
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lease_cus_event_logs');
    }

}
