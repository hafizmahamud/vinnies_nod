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
            $table->dateTime('confirmed_date_at')->nullable()->after('status_check_initiated_at');;
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
            $table->dropColumn('confirmed_date_at');
        });
    }
};
