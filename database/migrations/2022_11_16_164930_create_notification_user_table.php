<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_user', function (Blueprint $table) {
					$table->foreignUuid('notification_id')->constrained('notifications')->onDelete('cascade');
					$table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
					$table->boolean('is_view')->default(0);
					$table->boolean('send')->default(0);
					$table->primary(['notification_id', 'user_id'],'notification_user_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_user');
    }
}
