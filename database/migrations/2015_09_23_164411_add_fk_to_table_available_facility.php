<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToTableAvailableFacility extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('available_facilities', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_categories')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
            $table->dropForeign('available_facilities_vendor_id_foreign');
            $table->dropForeign('available_facilities_sub_category_id_foreign');
        });
    }
}
