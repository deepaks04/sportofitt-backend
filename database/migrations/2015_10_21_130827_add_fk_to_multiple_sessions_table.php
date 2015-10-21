<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToMultipleSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('multiple_sessions', function (Blueprint $table) {
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
        Schema::table('multiple_sessions', function (Blueprint $table) {
            $table->dropForeign('multiple_sessions_available_facility_id_foreign');
        });
    }
}
