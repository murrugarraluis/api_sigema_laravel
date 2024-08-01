<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceSheetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_sheet_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('article_id')->nullable()->constrained('articles');
            $table->foreignUuid('maintenance_sheet_id')->constrained('maintenance_sheets');
            $table->integer('quantity');
            $table->double('price');
            $table->text('description')->nullable();
            $table->integer('item')->nullable();
            $table->timestamps();
//            $table->primary(['article_id', 'maintenance_sheet_id'],'article_maintenance_sheet_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maintenance_sheet_details');
    }
}
