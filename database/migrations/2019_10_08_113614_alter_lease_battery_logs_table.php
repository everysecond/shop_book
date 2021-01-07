<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLeaseBatteryLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("lease_battery_logs", function (Blueprint $table) {
            $table->integer('model_six')->unsigned()->nullable()->default(0)->comment('48V45A租赁数');
            $table->integer('model_seven')->unsigned()->nullable()->default(0)->comment('60V32A租赁数');
            $table->integer('model_eight')->unsigned()->nullable()->default(0)->comment('60V45A租赁数');
            $table->integer('model_nine')->unsigned()->nullable()->default(0)->comment('72V32A租赁数');
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
