<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ControlsField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('controls__fields', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('value')->nullable();
            $table->foreignId('control_field_id');
            $table->foreignId('control_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    
    public function down()
    {
        Schema::dropIfExists('controls__fields');
    }
    
}
