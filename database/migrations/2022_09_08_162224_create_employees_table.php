<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('document_number');
            $table->string('name');
            $table->string('lastname');
            $table->string('personal_email');
            $table->string('phone');
            $table->string('address');
            $table->string('type');  // permanent ; relay
            $table->string('turn');  // day; night
            $table->string('native_language');
            $table->timestamps();
            $table->softDeletes();


//            FK
            $table->foreignUuid('user_id')->nullable()->unique()->constrained('users');
            $table->foreignUuid('position_id')->constrained('positions');
            $table->foreignUuid('document_type_id')->constrained('document_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
}
