<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleMaintenanceSheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_maintenance_sheet', function (Blueprint $table) {
            $table->foreignUuid('article_id')->constrained('articles');
            $table->foreignUuid('maintenance_sheet_id')->constrained('maintenance_sheets');
            $table->integer('quantity');
            $table->double('price');
            $table->text('description')->nullable();
            $table->primary(['article_id', 'maintenance_sheet_id'],'article_maintenance_sheet_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_maintenance_sheet');
    }
}
