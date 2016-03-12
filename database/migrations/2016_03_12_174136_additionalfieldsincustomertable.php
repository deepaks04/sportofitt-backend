<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Additionalfieldsincustomertable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('country_id')
                    ->unsingned()
                    ->after('phone_no');
            $table->integer('state_id')
                    ->unsingned()
                    ->after('country_id');
            $table->integer('city_id')
                    ->unsingned()
                    ->after('state_id');
            $table->integer('address')
                    ->unsingned()
                    ->after('area_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('available_facilities', function (Blueprint $table) {
            $table->dropColumn('country_id', 'state_id', 'city_id', 'address');
        });
    }

}