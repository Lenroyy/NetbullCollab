<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Task extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('subject')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assigned_id')->nullable();
            $table->string('status');
            $table->string('priority');
            $table->string('progress')->nullable();
            $table->foreignId('site_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completed_date')->nullable();
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
        Schema::dropIfExists('tasks');
    }
}
