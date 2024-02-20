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
        Schema::table('twinnings', function (Blueprint $table) {
            $statuses = Helper::getTwinningPeriodTypeList();

            $table->enum('twinning_period', array_keys($statuses))->after('is_active_at')->nullable();
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
            $table->dropColumn('twinning_period');
        });
    }
};

