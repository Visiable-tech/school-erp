<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance_sessions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->restrictOnDelete();

            $table->foreignId('section_id')
                ->constrained('sections')
                ->restrictOnDelete();

            $table->date('attendance_date');

            $table->text('remarks')->nullable();

            $table->foreignId('marked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                [
                    'school_id',
                    'academic_year_id',
                    'school_class_id',
                    'section_id',
                    'attendance_date'
                ],
                'student_att_session_unique'
            );

            $table->index(
                ['school_id', 'attendance_date'],
                'student_att_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance_sessions');
    }
};