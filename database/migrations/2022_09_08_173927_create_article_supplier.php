<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleSupplier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_supplier', function (Blueprint $table) {
            $table->foreignUuid('article_id')->constrained('articles');
            $table->foreignUuid('supplier_id')->constrained('suppliers');
            $table->double('price');
            $table->primary(['article_id', 'supplier_id'],'article_supplier_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_supplier');
    }
}
