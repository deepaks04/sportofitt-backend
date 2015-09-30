<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToDayWiseSessionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('day_wise_sessions', function (Blueprint $table) {
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
        Schema::table('day_wise_sessions', function (Blueprint $table) {
            $table->dropForeign('day_wise_sessions_available_facility_id_foreign');
        });
    }
}
