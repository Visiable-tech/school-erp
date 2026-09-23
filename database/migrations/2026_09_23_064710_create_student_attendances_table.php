<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendances', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('attendance_session_id')
                ->constrained('student_attendance_sessions')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->restrictOnDelete();

            $table->enum('attendance_status', [
                'present',
                'absent',
                'late',
                'leave',
                'half_day'
            ])->default('present');

            $table->string('remarks', 500)->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'attendance_session_id',
                    'student_id'
                ],
                'student_att_student_unique'
            );

            $table->index(
                ['school_id', 'student_id'],
                'student_att_student_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendances');
    }
};