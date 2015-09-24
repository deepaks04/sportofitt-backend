<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsActiveToAvailableFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('available_facilities', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after('vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('available_facilities', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
