<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpdatedByColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
        });

		Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
        });

        Schema::table('local_conferences', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
        });

        Schema::table('overseas_conferences', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
        });

        Schema::table('twinnings', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
        });

        Schema::table('new_remittances', function (Blueprint $table) {
            $table->unsignedInteger('updated_by')->nullable()->default(1);
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
            $table->dropColumn(['updated_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });

        Schema::table('local_conferences', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });

        Schema::table('overseas_conferences', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });

        Schema::table('twinnings', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });

        Schema::table('new_remittances', function (Blueprint $table) {
            $table->dropColumn('updated_by');
        });
    }
}
