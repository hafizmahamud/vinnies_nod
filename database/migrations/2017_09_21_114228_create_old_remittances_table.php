<?php

use App\Vinnies\Helper;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOldRemittancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('old_remittances', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('state', array_keys(Helper::getAUStates()))->nullable();
            $table->integer('quarter');
            $table->integer('year');
            $table->decimal('allocated', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('cheque_number')->nullable();
            $table->text('comments')->nullable();
            $table->dateTime('received_at')->nullable();
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
        Schema::dropIfExists('old_remittances');
    }
}
