<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_sheets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->dateTime('date')->nullable();
            $table->string('responsible');
            $table->string('technical');
            $table->text('description');
            $table->string('ref_invoice_number')->nullable();
            $table->string('maximum_working_time')->nullable();

            $table->foreignUuid('maintenance_type_id')->constrained('maintenance_types');
            $table->foreignUuid('supplier_id')->constrained('suppliers');
            $table->foreignUuid('machine_id')->constrained('machines');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_sheets');
    }
}
