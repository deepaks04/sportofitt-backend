<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsVenueFieldInAvaialbleFacitlies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('available_facilities', function (Blueprint $table) {
            $table->integer('is_venue')
                    ->default(1)
                    ->after('id')
                    ->comment(', 1 => Venue, 0 => Coaching Service');
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
            $table->dropColumn('is_venue');
        });
    }
}
