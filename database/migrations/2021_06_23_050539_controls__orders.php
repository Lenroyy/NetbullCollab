<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ControlsOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controls__orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->string('order_no')->nullable();
            $table->integer('quantity');
            $table->foreignId('control_type');
            $table->date('date_due');
            $table->text('notes')->nullable();
            $table->foreignId('site_id');
            $table->foreignId('user_id');
            $table->foreignId('organisation_id')->nullable();
            $table->string('simpro_id')->nullable();
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
        Schema::dropIfExists('controls__orders');
    }
}
