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
        Schema::create('session_package_child', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('session_package_id')->unsigned();
            $table->boolean('is_peak')->default(0);
            $table->integer('month')->nullable();
            $table->integer('day')->nullable();
            $table->time('start')->nullable();
            $table->time('end')->nullable();
            $table->float('actual_price');
            $table->integer('discount')->default(0);
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
        Schema::drop('session_package_child');
    }
}
