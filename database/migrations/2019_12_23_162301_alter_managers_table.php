<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("managers", function (Blueprint $table) {
            $table->integer('agent_id')->unsigned()->nullable(false)->default(0)->comment('管理区域');
            $table->string('code', 16)->nullable()->comment('验证码');
            $table->string('access_token', 50)->nullable()->comment('access_token');
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
