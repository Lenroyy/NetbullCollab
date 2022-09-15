<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ThingsboardsDevice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thingsboards__devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('thingsboard_id');
            $table->boolean('archived'); 
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->foreignId('control_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thingsboards__devices');
    }
}
