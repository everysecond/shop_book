<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLeaseContractDateInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("lease_contract_date_infos", function (Blueprint $table) {
            $table->unsignedDecimal("today_deposit",10,2)->default(0)
                ->nullable(false)->after("total_rental")->comment("当日租赁押金");
            $table->unsignedDecimal("total_deposit",12,2)->default(0)
                ->nullable(false)->after("total_rental")->comment("累计到当日租赁押金");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
