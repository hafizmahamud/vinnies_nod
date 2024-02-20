<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusCheckFieldsToOverseasConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overseas_conferences', function (Blueprint $table) {
            $table->boolean('is_in_status_check')->after('is_active')->default(false);
            $table->dateTime('status_check_initiated_at')->after('untwinned_at')->nullable();
            $table->boolean('is_in_surrendering')->after('is_active')->default(false);
            $table->dateTime('surrendering_initiated_at')->after('untwinned_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overseas_conferences', function (Blueprint $table) {
            $table->dropColumn([
                'is_in_status_check',
                'status_check_initiated_at',
                'is_in_surrendering',
                'surrendering_initiated_at',
            ]);
        });
    }
}
