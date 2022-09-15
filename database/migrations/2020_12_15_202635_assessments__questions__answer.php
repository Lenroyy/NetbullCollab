<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AssessmentsQuestionsAnswer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments__questions__answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->foreignId('question_id');
            $table->foreignId('option_id');
            $table->string('action')->nullable();
            $table->string('score')->nullable();
            $table->longText('comments')->nullable();
            $table->foreignId('goto_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments__questions');
    }
}
