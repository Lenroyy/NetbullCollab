<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLicenseProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('license__profiles', function(Blueprint $table) {
            $table->renameColumn('user_cost', 'user_discount')->nullable();
            // $table->string('user_discount')->nullable()->change();
            $table->string('hardware_discount')->nullable();
            $table->string('marketplace_discount')->nullable();
            $table->string('changed_by')->nullable();
            $table->text('changed')->nullable();
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
