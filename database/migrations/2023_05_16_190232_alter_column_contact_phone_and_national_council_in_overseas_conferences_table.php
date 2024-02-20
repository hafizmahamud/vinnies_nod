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
        
        // Schema::table('overseas_conferences', function (Blueprint $table) {
        //     $table->string('contact_phone')->nullable()->change();
        // });
        DB::statement("ALTER TABLE overseas_conferences CHANGE COLUMN contact_phone contact_phone VARCHAR(191) NULL ");
        DB::statement("ALTER TABLE overseas_conferences CHANGE COLUMN national_council national_council INTEGER NULL ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('overseas_conferences', function (Blueprint $table) {
        //     $table->dropColumn('contact_phone');
        // });
    }
};
