<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Userbodystatstable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_body_stats', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('weight');
            $table->float('height');
            $table->float('waist');
            $table->float('chest');
            $table->float('forarm');
            $table->float('wrist');
            $table->float('hip');
            $table->string('activity_level');
            $table->float('bmi');
            $table->float('body_fat');
            $table->float('bmr');
            $table->float('tdee');
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
        Schema::drop('user_body_stats');
    }

}
