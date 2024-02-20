<?php

use App\Vinnies\Helper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTwinningStatusToOverseasConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overseas_conferences', function (Blueprint $table) {
            $statuses = Helper::getOSConferencesTwinningStatuses();

            $table->enum('twinning_status', array_keys($statuses))->after('country_id')->default('n/a');
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
            $table->dropColumn('twinning_status');
        });
    }
}
