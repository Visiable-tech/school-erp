<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {

            $table->unique(
                [
                    'school_id',
                    'academic_year_id',
                    'school_class_id',
                    'section_id',
                    'roll_no'
                ],
                'student_enrollment_roll_unique'
            );

        });
    }


    public function down(): void
    {
        Schema::table('student_enrollments', function (Blueprint $table) {

            $table->dropUnique(
                'student_enrollment_roll_unique'
            );

        });
    }
};