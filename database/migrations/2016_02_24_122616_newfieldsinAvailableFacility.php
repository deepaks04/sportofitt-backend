<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewfieldsinAvailableFacility extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('available_facilities', function (Blueprint $table) {
            $table->integer('area_id')
                    ->default(0)
                    ->after('vendor_id');
            $table->string('pincode')
                    ->nullable()
                    ->after('area_id');
            $table->tinyInteger('is_featured')
                    ->default(0)
                    ->after('peak_hour_price');
            $table->integer('root_category_id')
                    ->unsigned()
                    ->default(0)
                    ->after('description');
            
            
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
            $table->dropColumn('area_id', 'pincode','is_featured','root_category_id');
        });
    }

}
