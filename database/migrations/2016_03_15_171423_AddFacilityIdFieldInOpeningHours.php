<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFacilityIdFieldInOpeningHours extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('opening_hours', function (Blueprint $table) {
            $table->integer('available_facility_id')
                    ->unsingned()
                    ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('opening_hours', function(Blueprint $table) {
            $table->dropColumn('available_facility_id');
        });
    }

}