<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSesssionBookingTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('booked_or_blocked')->unsigned();//1 For Booked And 2 for Blocked.
            $table->integer('day');
            $table->dateTime('startAt');
            $table->dateTime('endAt');
            //$table->date('date');
            /*
             * Nullable Because In case of vendor Block it will be nullable
             * In case of user booking it wont be nullable
            */
            $table->integer('peak')->unsigned()->nullable();
            $table->integer('off_peak')->unsigned()->nullable();
            $table->float('price')->nullable();
            $table->integer('discount')->nullable();
            $table->float('final_price')->nullable();
            $table->integer('multiple_session_id')->unsigned()->nullable();
            $table->integer('opening_hour_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('available_facility_id')->unsigned();
            $table->boolean('is_active')->default(1);
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
        Schema::drop('session_bookings');
    }
}
