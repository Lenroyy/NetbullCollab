<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('licenses', function(Blueprint $table) {
            // $table->renameColumn('default_user_cost', 'user_1_10');
            $table->string('user_11_20');
            $table->string('user_21_30');
            $table->string('user_31_40');
            $table->string('user_41_50');
            $table->string('user_50_100');
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
