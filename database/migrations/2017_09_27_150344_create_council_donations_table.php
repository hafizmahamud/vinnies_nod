<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouncilDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('council_donations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('new_remittance_id');
            $table->unsignedInteger('twinning_id');
            $table->unsignedInteger('document_id');
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('council_donations');
    }
}