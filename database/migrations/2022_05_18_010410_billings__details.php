<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BillingsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings__details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->foreignId('billing_id');
            $table->string('type');
            $table->string('reference');
            $table->string('cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billings__details');
    }
}
