<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SitesReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites__reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->foreignId('site_id');
            $table->string('report_name');
            $table->string('frequency');
            $table->string('format');
            $table->string('email_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
