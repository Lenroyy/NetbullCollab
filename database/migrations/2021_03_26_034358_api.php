<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Api extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('application_name');
            $table->boolean('archived'); 
            $table->string('base_url')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->text('token')->nullable();
            $table->string('token_ttl')->nullable();
            $table->text('refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apis');
    }
}
