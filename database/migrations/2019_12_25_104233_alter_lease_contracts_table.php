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
            $table->integer('city_id')->unsigned()->nullable()->after('agent_id')->comment('市id');
            $table->integer('county_id')->unsigned()->nullable()->after('agent_id')->comment('县/区id');
            $table->decimal('deposit_price',10,2)->unsigned()->nullable(false)->default(0)->comment('无旧电池，电池押金');
            $table->tinyInteger('lease_type')->unsigned()->nullable(false)->default(0)->comment('租赁类型');
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
