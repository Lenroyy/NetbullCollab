<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Log extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('profiles_id')->nullable();
            $table->string('module')->nullable();
            $table->string('module_id')->nullable();
            $table->string('action')->nullable();
            $table->longText('notes')->nullable();
            $table->string('log_level')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
