<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatesToLocalConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_conferences', function (Blueprint $table) {
            $table->dateTime('is_active_at')->after('comments')->nullable();
            $table->dateTime('is_abeyant_at')->after('comments')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('twinnings', function (Blueprint $table) {
            $table->dropColumn(['is_active_at', 'is_abeyant_at']);
        });
    }
}
