<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainingsProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings__profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('training_id');
            $table->foreignId('hygenist_id');
            $table->foreignId('profile_id');
            $table->foreignId('active_organisation_id');
            $table->foreignId('training_hygenist_id');
            $table->text('instructions')->nullable();
            $table->string('price');
            $table->string('status');
            $table->boolean('paid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trainings__profiles');
    }
}
