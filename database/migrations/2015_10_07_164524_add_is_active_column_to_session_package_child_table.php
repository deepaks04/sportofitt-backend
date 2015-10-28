<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsActiveColumnToSessionPackageChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_child', function (Blueprint $table) {
            $table->boolean('is_peak')->default(1)->after('discount');
            $table->boolean('is_active')->default(1)->after('is_peak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_child', function (Blueprint $table) {
            $table->dropColumn('is_peak');
            $table->dropColumn('is_active');
        });
    }
}
