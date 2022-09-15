<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainingHygenist extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings__hygenists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('profile_id');
            $table->integer('training_id');
            $table->double('price')->nullable();
            $table->string('link')->nullable();
            $table->boolean('active_provider');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_hygenists');
    }
}