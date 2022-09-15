<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ActionsAssessment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions__assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('user_id');
            $table->foreignId('site_id');
            $table->foreignId('assessment_id');
            $table->foreignId('active_organisation_id')->nullable();
            $table->foreignId('history_assessment_id')->nullable();
            $table->integer('zone_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions__assessments');
    }
}

