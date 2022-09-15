<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class History extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('profiles_id');
            $table->foreignId('site_id');
            $table->foreignId('zone_id')->nullable();
            $table->foreignId('activity_id');
            $table->date('time_start');
            $table->date('time_end');
            $table->boolean('archived');
            $table->boolean('checked');
            $table->string('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('histories');
    }
}
