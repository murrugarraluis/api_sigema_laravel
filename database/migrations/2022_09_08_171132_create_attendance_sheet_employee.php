<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceSheetEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_sheet_employee', function (Blueprint $table) {
            $table->foreignUuid('attendance_sheet_id')->constrained('attendance_sheets');
            $table->foreignUuid('employee_id')->constrained('employees');
            $table->Datetime('check_in')->nullable();
            $table->Datetime('check_out')->nullable();
            $table->boolean('attendance')->default(0);// missed=0 ; attended=1 ;
            $table->string('missed_reason')->nullable();
            $table->text('missed_description')->nullable();
            $table->primary(['attendance_sheet_id', 'employee_id'],'attendance_sheet_employee_pk');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_sheet_employee');
    }
}
