<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeaseProcessChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("lease_process_channels", function (Blueprint $table) {
            $table->integer("register_num",false,true)->default(0)
                ->nullable(false)->after("systemtype")->comment("注册用户");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("lease_process_channels", function (Blueprint $table) {
            $table->dropColumn("register_num");
        });
    }
}
