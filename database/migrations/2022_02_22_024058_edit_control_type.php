<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditControlType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controls__types', function (Blueprint $table) {
            $table->foreignId('internal_lease_cost_center_id')->nullable();
            $table->foreignId('external_lease_cost_center_id')->nullable();
            $table->foreignId('sale_cost_center_id')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('controls__types', function (Blueprint $table) {
            //
        });
    }
}
