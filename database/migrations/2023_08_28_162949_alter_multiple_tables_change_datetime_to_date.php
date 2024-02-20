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
        Schema::table('projects', function (Blueprint $table) {
            $table->date('received_at')->change();
            $table->date('completed_at')->change();
            $table->date('estimated_completed_at')->change();
            $table->date('project_completion_date')->change();
        });
        Schema::table('local_conferences', function (Blueprint $table) {
            $table->date('is_abeyant_at')->change();
            $table->date('last_confirmed_at')->change();
            $table->date('is_active_at')->change();
        });
        Schema::table('overseas_conferences', function (Blueprint $table) {
            $table->date('is_active_at')->change();
            $table->date('is_abeyant_at')->change();
            $table->date('twinned_at')->change();
            $table->date('untwinned_at')->change();
            $table->date('surrendering_initiated_at')->change();
            $table->date('surrendering_deadline_at')->change();
            $table->date('status_check_initiated_at')->change();
            $table->date('confirmed_date_at')->change();
        });
        Schema::table('twinnings', function (Blueprint $table) {
            $table->date('is_active_at')->change();
            $table->date('is_surrendered_at')->change();
        });
        Schema::table('new_remittances', function (Blueprint $table) {
            $table->date('date')->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->date('conditions_accepted_at')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dateTime('received_at')->change();
            $table->dateTime('completed_at')->change();
            $table->dateTime('estimated_completed_at')->change();
            $table->dateTime('project_completion_date')->change();
        });
        Schema::table('local_conferences', function (Blueprint $table) {
            $table->dateTime('is_abeyant_at')->change();
            $table->dateTime('last_confirmed_at')->change();
            $table->dateTime('is_active_at')->change();
        });
        Schema::table('overseas_conferences', function (Blueprint $table) {
            $table->dateTime('is_active_at')->change();
            $table->dateTime('is_abeyant_at')->change();
            $table->dateTime('twinned_at')->change();
            $table->dateTime('untwinned_at')->change();
            $table->dateTime('surrendering_initiated_at')->change();
            $table->dateTime('surrendering_deadline_at')->change();
            $table->dateTime('status_check_initiated_at')->change();
            $table->dateTime('confirmed_date_at')->change();
        });
        Schema::table('twinnings', function (Blueprint $table) {
            $table->dateTime('is_active_at')->change();
            $table->dateTime('is_surrendered_at')->change();
        });
        Schema::table('new_remittances', function (Blueprint $table) {
            $table->dateTime('date')->change();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('conditions_accepted_at')->change();
        });
    }
};
