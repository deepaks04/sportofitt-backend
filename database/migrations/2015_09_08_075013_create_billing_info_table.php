<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_info', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_title',255)->nullable();
            $table->string('registration_no',255)->nullable();
            $table->string('service_tax_no',255)->nullable();
            $table->string('address',255)->nullable();
            $table->string('pan_no',255)->nullable();
            $table->string('contact_person_name',255)->nullable();
            $table->string('contact_person_email',255)->nullable();
            $table->string('contact_person_phone',255)->nullable();
            $table->string('vat',10)->nullable();
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
        Schema::drop('billing_info');
    }
}
