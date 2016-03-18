<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Bookedpackages extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booked_packages', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('session_package_id')->unsigned();
            $table->foreign('session_package_id')->references('id')->on('session_package');
            $table->double('booking_amount');
            $table->double('discount');
            $table->double('discounted_amount');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('available_facility_id')->unsigned();
            $table->foreign('available_facility_id')->references('id')->on('available_facilities');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('booked_packages');
    }

}

