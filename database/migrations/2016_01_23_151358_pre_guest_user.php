<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PreGuestUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pre_guest_users', function (Blueprint $table) {
             //$table->increments('id');
            $table->string('full_name',255);
            $table->string('email',255);
            $table->string('phone',255);
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
        Schema::drop('pre_guest_users');
    }
}
