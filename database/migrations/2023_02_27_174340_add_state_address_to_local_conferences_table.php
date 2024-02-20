<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_conferences', function (Blueprint $table) {
            $states = Helper::getAllStates();

            $table->enum('state_address', array_keys($states))->after('postcode')->nullable();
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
            $table->dropColumn('state_address');
        });
    }
};
