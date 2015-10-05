<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionPackageTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('session_package', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('available_facility_id')->unsigned();
            $table->integer('package_type_id')->unsigned();
            $table->string('name',255);
            $table->text('description')->nullable();
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
        Schema::drop('session_package');
    }
}
