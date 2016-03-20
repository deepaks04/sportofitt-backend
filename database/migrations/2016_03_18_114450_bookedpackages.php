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
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('month');
            $table->double('booking_amount');
            $table->double('discount');
            $table->double('final_amount');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('available_facility_id')->unsigned();
            $table->foreign('available_facility_id')->references('id')->on('available_facilities');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->tinyInteger('is_peak')->default(0);
            $table->tinyInteger('is_cancelled')->default(0);
            $table->dateTime('cancellation_date')->nullable();
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