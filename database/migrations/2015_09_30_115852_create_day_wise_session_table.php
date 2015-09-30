<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDayWiseSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('day_wise_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('available_facility_id')->unsigned();
            $table->float('actual_price')->nullable();
            $table->float('discounted_price')->nullable();
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
        Schema::drop('day_wise_sessions');
    }
}
