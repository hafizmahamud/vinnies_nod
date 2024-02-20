<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Vinnies\Helper;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $statuses               = Helper::getProjectsStatuses();
            $consolidated_statuses  = Helper::getProjectsConsolidatedStatuses();

            $table->boolean('completion_report_received')->after('is_awaiting_support')->default(0);
            $table->enum('consolidated_status', array_keys($consolidated_statuses))->after('is_awaiting_support')->default('pending');
            $table->enum('status', array_keys($statuses))->after('is_awaiting_support')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('consolidated_status');
            $table->dropColumn('completion_report_received');
        });
    }
};
