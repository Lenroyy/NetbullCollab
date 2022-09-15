<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ThingsboardsDeviceReading extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thingsboards__device__readings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('device_id');
            $table->foreignId('history_id')->nullable();
            $table->string('reading')->nullable();
            $table->string('reading_type_id')->nullable();
            $table->string('reading_timestamp')->nullable();
            $table->string('outcome')->nullable();
            $table->foreignId('control_id')->nullable();
            $table->string('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thingsboards__device__readings');
    }
}
