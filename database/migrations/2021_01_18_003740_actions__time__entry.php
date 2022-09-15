<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ActionsTimeEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions__time__entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('user_id');
            $table->foreignId('site_id');
            $table->date('date');
            $table->string('start');
            $table->string('finish')->nullable();
            $table->foreignId('active_organisation_id')->nullable();
            $table->foreignId('zone_id')->nullable();
            $table->foreignId('history_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions__time__entries');
    }
}
