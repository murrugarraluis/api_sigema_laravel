<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_supplier', function (Blueprint $table) {
            $table->foreignUuid('supplier_id')->constrained('suppliers');
            $table->foreignUuid('bank_id')->constrained('banks');
            $table->string('account_number');
            $table->string('interbank_account_number');
            $table->primary(['supplier_id', 'bank_id'],'bank_supplier_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_supplier');
    }
}
