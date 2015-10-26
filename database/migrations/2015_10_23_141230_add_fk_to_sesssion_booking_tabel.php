<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToSesssionBookingTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_bookings', function (Blueprint $table) {
            $table->foreign('multiple_session_id')->references('id')->on('multiple_sessions')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('available_facility_id')->references('id')->on('available_facilities')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropForeign('session_bookings_multiple_session_id_foreign');
            $table->dropForeign('session_bookings_user_id_foreign');
            $table->dropForeign('session_bookings_available_facility_id_foreign');
        });
    }
}
