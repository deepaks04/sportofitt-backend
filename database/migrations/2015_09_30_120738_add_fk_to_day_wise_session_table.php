<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkToDayWiseSessionTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('package_child', function (Blueprint $table) {
            $table->foreign('session_package_id')->references('id')->on('session_package')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropForeign('package_child_session_package_id_foreign');
        });
    }
}
