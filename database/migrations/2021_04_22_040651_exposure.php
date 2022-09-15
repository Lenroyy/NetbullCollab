<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Exposure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exposures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->string('name');
            $table->boolean('archived');
            $table->foreignId('reading_type_id');
            $table->string('time_period');
            $table->string('level');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exposures');
    }
}

