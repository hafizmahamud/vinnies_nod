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
            $statuses = Helper::getCountry();

            $table->enum('country', array_keys($statuses))->after('postcode')->nullable();
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
            $table->dropColumn('country');
        });
    }
};

