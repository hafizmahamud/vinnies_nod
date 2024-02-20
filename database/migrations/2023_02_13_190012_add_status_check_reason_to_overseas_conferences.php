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
        Schema::table('overseas_conferences', function (Blueprint $table) {
            $statuses = Helper::getOSConferencesStatusCheckReason();

            $table->enum('status_check_reason', array_keys($statuses))->after('is_in_status_check')->default('n/a');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status_check_reason', function (Blueprint $table) {
            //
        });
    }
};
