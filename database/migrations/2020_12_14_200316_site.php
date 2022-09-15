<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Site extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->foreignId('builder_id');
            $table->foreignId('hygenist_id')->nullable();
            $table->integer('simpro_site_id_1')->nullable();
            $table->integer('simpro_site_id_2')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->foreignId('primary_contact_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('image')->nullable();
            $table->boolean('archived');
            $table->string('status')->nullable();
            $table->integer('relinquish_id')->nullable();
            $table->boolean('zone_qr_code_function');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}
