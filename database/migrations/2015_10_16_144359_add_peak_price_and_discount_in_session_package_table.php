<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPeakPriceAndDiscountInSessionPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_package', function (Blueprint $table) {
            $table->float('peak_price')->nullable()->after('duration');
            $table->integer('peak_discount')->default(0)->after('peak_price');
            $table->float('off_peak_price')->nullable()->after('peak_discount');
            $table->integer('off_peak_discount')->default(0)->after('off_peak_price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('session_package', function (Blueprint $table) {
            $table->dropColumn('peak_price');
            $table->dropColumn('peak_discount');
            $table->dropColumn('off_peak_price');
            $table->dropColumn('off_peak_discount');
        });
    }
}
