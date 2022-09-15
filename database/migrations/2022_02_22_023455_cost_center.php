<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CostCenter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cost__centers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->string('cost_center_name');
            $table->integer('company_id');
            $table->integer('cost_center_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cost_centers');
    }
}
