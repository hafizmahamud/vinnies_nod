<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_donations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('old_remittance_id')->nullable();
            $table->unsignedInteger('beneficiary_id')->nullable();
            $table->string('purpose')->nullable();
            $table->string('myob_code')->nullable();
            $table->enum('state', array_keys(Helper::getAUStates()))->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->integer('twins')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('old_donations');
    }
}
