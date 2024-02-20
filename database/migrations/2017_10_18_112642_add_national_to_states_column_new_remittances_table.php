<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNationalToStatesColumnNewRemittancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_remittances', function (Blueprint $table) {
            DB::statement('ALTER TABLE new_remittances MODIFY state ENUM("' . implode('","', array_keys(Helper::getStates())) . '")');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_remittances', function (Blueprint $table) {
            DB::statement('ALTER TABLE new_remittances MODIFY state ENUM("' . implode('","', array_keys(Helper::getAUStates())) . '")');
        });
    }
}
