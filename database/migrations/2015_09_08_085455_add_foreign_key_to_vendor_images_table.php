<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToVendorImagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendor_images', function (Blueprint $table) {
            $table->foreign('vendor_id')
                ->references('id')
                ->on('vendors')
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
        Schema::table('vendor_images', function (Blueprint $table) {
            $table->dropForeign('vendor_images_vendor_id_foreign');
        });
    }
}
