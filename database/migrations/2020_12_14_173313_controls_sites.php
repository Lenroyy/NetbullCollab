<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ControlsSites extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controls__sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('control_id');
            $table->foreignId('from_site_id');
            $table->foreignId('to_site_id');
            $table->foreignId('from_map_id');
            $table->foreignId('to_map_id');
            $table->foreignId('from_zone_id');
            $table->foreignId('to_zone_id');
            $table->foreignId('from_hazard_id');
            $table->foreignId('to_hazard_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controls__sites');
    }
}
