<?php

use App\Vinnies\Helper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewRemittancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_remittances', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('state', array_keys(Helper::getAUStates()));
            $table->integer('quarter');
            $table->integer('year');
            $table->dateTime('date')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->text('comments')->nullable();
            $table->unsignedInteger('projects_document_id')->nullable();
            $table->unsignedInteger('grants_document_id')->nullable();
            $table->unsignedInteger('councils_document_id')->nullable();
            $table->unsignedInteger('twinnings_document_id')->nullable();
            $table->dateTime('approved_at')->nullable();
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
        Schema::dropIfExists('new_remittances');
    }
}
