<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechnicalSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technical_sheets', function (Blueprint $table) {
            $table->string('technical_sheetable_type');
            $table->uuid('technical_sheetable_id');
            $table->string('path');
            $table->primary(['technical_sheetable_type','technical_sheetable_id'],'images_fk');
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
        Schema::dropIfExists('technical_sheets');
    }
}
