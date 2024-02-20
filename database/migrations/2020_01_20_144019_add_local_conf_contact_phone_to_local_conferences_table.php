<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalConfContactPhoneToLocalConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_conferences', function (Blueprint $table) {
            $table->string('contact_phone')->after('contact_email')->nullable();
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
            $table->dropColumn(['contact_phone']);
        });
    }
}
