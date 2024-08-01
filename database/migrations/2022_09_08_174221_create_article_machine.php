<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleMachine extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_machine', function (Blueprint $table) {
            $table->foreignUuid('article_id')->constrained('articles');
            $table->foreignUuid('machine_id')->constrained('machines');
            $table->primary(['article_id', 'machine_id'],'article_machine_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_machine');
    }
}
