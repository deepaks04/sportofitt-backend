<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('billing_info_id')->references('id')->on('billing_info')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('bank_detail_id')->references('id')->on('bank_details')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('vendors_user_id_foreign');
            $table->dropForeign('vendors_area_id_foreign');
            $table->dropForeign('vendors_billing_info_id_foreign');
            $table->dropForeign('vendors_bank_detail_id_foreign');
        });
    }
}
