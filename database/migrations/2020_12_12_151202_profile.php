<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Profile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->string('type');
            $table->string('name');
            $table->boolean('archived');
            $table->integer('simpro_id_1')->nullable();
            $table->integer('simpro_id_2')->nullable();
            $table->integer('primary_contact')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();
            $table->string('country')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('theme')->nullable();
            $table->integer('security_group')->nullable();
            $table->string('member_hash')->nullable();
            $table->string('provider_type')->nullable();
            $table->boolean('super_user')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
