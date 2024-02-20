<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNationalToStatesColumnLocalConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_conferences', function (Blueprint $table) {
            DB::statement('ALTER TABLE local_conferences MODIFY state ENUM("' . implode('","', array_keys(Helper::getStates())) . '")');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_conferences', function (Blueprint $table) {
            DB::statement('ALTER TABLE local_conferences MODIFY state ENUM("' . implode('","', array_keys(Helper::getAUStates())) . '")');
        });
    }
}
