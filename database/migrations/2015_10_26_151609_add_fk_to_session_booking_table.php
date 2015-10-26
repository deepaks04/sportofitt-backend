<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToSessionBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_bookings', function (Blueprint $table) {
            $table->foreign('opening_hour_id')->references('id')->on('opening_hours')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session_bookings', function (Blueprint $table) {
            $table->dropForeign('session_bookings_opening_hour_id_foreign');
        });
    }
}
