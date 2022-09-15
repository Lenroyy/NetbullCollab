<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ThingsboardsDeviceReadingTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thingsboards__device__reading__types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->foreignId('device_id');
            $table->foreignId('reading_type_id');
            $table->string('calculation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thingsboards__device__reading__types');
    }
}
