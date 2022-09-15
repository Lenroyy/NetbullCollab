<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ControlsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controls__types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name')->nullable();
            $table->foreignId('controls_type_id');
            $table->string('manufacturer')->nullable();
            $table->string('simpro_asset_type_id_1')->nullable();
            $table->string('simpro_asset_type_id_2')->nullable();
            $table->string('billing_amount')->nullable();
            $table->string('billing_frequency')->nullable();
            $table->string('image')->nullable();
            $table->string('shape')->nullable();
            $table->foreignId('control_type_group')->nullable();
            $table->string('simpro_default_cost_center_id')->nullable();
            $table->string('simpro_prebuild_id_1')->nullable();
            $table->string('simpro_prebuild_id_2')->nullable();
            $table->boolean('archived'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controls__types');
    }
}
