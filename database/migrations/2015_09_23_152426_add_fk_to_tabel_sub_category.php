<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToTabelSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->foreign('root_category_id')->references('id')->on('root_categories')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropForeign('sub_categories_root_category_id_foreign');
        });
    }
}
