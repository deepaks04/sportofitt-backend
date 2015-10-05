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
            $table->string('duration');
            $table->integer('day')->nullable();
            $table->float('actual_price');
            $table->integer('discount');
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
