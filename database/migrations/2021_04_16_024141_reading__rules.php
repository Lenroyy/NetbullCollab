<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReadingRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reading__rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps(); 
            $table->string('name');
            $table->foreignId('reading_type_id');
            $table->string('rule_type');
            $table->foreignId('assessment_id')->nullable();
            $table->foreignId('question_id')->nullable();
            $table->foreignId('answer_id')->nullable();
            $table->integer('within_range_max')->nullable();
            $table->integer('within_range_min')->nullable();
            $table->integer('above_max')->nullable();
            $table->integer('below_min')->nullable();
            $table->string('formula')->nullable();
            $table->string('outcome')->nullable();
            $table->integer('order');
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
        Schema::dropIfExists('reading__rules');
    }
}
