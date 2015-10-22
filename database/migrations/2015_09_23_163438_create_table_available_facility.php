<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAvailableFacility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('available_facilities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',255);
            $table->string('image',255);
            $table->integer('sub_category_id')->unsigned();
            $table->integer('vendor_id')->unsigned();
            $table->integer('slots');
            $table->integer('cancellation_before_24hrs')->unsigned();
            $table->integer('cancellation_after_24hrs')->unsigned();
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
        Schema::drop('available_facilities');
    }
}
