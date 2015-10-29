<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankDetailsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bank_name', 255)->nullable();
            $table->string('ifsc', 255)->nullable();
            $table->string('account_type', 255)->nullable();
            $table->string('branch_name', 255)->nullable();
            $table->string('beneficiary', 255)->nullable();
            $table->string('account_number', 255)->nullable();
            $table->integer('vendor_id')->unsigned();
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
        Schema::drop('bank_details');
    }
}
