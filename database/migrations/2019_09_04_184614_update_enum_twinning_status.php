<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEnumTwinningStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE overseas_conferences CHANGE COLUMN twinning_status twinning_status ENUM('twinned','untwinned','awaiting_twin','non_financial','n/a') NOT NULL DEFAULT 'n/a'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE overseas_conferences CHANGE COLUMN twinning_status twinning_status ENUM('twinned','untwinned','awaiting_twin','n/a') NOT NULL DEFAULT 'n/a'");
    }
}
