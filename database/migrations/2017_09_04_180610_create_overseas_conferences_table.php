<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOverseasConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('overseas_conferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('central_council')->nullable();
            $table->string('particular_council')->nullable();
            $table->string('parish')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('suburb')->nullable();
            $table->string('postcode')->nullable();
            $table->string('state')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->text('comments')->nullable();
            $table->dateTime('twinned_at')->nullable();
            $table->dateTime('untwinned_at')->nullable();
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
        Schema::dropIfExists('overseas_conferences');
    }
}
