<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('beneficiary_id')->nullable();
            $table->unsignedInteger('overseas_conference_id')->nullable();
            $table->string('overseas_project_id')->nullable();
            $table->string('currency')->nullable();
            $table->string('exchange_rate')->nullable();
            $table->decimal('local_value', 10, 2)->nullable();
            $table->decimal('au_value', 10, 2)->nullable();
            $table->boolean('is_fully_paid')->default(false);
            $table->boolean('is_awaiting_support')->default(true);
            $table->text('comments')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('fully_paid_at')->nullable();
            $table->dateTime('completed_at')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
