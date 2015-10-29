<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMultipleSessionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiple_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('available_facility_id')->unsigned();
            $table->integer('peak')
                ->unsigned()
                ->default(0);
            $table->integer('off_peak')
                ->unsigned()
                ->default(0);
            $table->integer('price')->unsigned();
            $table->integer('discount')
                ->unsigned()
                ->default(0);
            $table->boolean('is_active')->default(1);
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
        Schema::drop('multiple_sessions');
    }
}
