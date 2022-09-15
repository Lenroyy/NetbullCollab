<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssessmentsActivities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments__activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('assessment_id');
            $table->foreignId('activities_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments__activities');
    }
}
