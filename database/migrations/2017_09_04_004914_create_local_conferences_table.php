<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocalConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('local_conferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('regional_council')->nullable();
            $table->unsignedInteger('diocesan_council_id')->nullable();
            $table->string('parish')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('suburb')->nullable();
            $table->string('postcode')->nullable();
            $table->enum('state', array_keys(Helper::getAUStates()))->nullable();
            $table->text('comments')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('local_conferences');
    }
}
