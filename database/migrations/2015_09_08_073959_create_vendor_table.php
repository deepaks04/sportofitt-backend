<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVendorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->increments('id');

            $table->string('business_name',255);
            $table->string('longitude',255);
            $table->string('latitude',255);
            $table->text('description');
            $table->integer('commission');
            $table->boolean('is_processed');
            $table->integer('user_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->integer('billing_info_id')->unsigned();
            $table->integer('bank_detail_id')->unsigned();
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
        Schema::drop('vendors');
    }
}
