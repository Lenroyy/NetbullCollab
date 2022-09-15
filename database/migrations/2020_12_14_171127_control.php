<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Control extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('serial')->nullable();
            $table->string('simpro_asset_id_1')->nullable();
            $table->string('simpro_asset_id_2')->nullable();
            $table->boolean('archived');
            $table->integer('deployed')->nullable();
            $table->date('commission_date')->nullable();
            $table->string('billing_amount')->nullable();
            $table->string('billing_frequency')->nullable();
            $table->foreignId('current_site')->nullable();
            $table->foreignId('controls_type_id')->nullable();
            $table->string('colour')->nullable();
            $table->string('x')->nullable();
            $table->string('y')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controls');
    }
}

