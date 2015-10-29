<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkSessionPackageTabel extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('session_package', function (Blueprint $table) {
            $table->foreign('available_facility_id')
                ->references('id')
                ->on('available_facilities')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('package_type_id')
                ->references('id')
                ->on('package_types')
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
        Schema::table('session_package', function (Blueprint $table) {
            $table->dropForeign('session_package_available_facility_id_foreign');
            $table->dropForeign('session_package_package_type_id_foreign');
        });
    }
}
