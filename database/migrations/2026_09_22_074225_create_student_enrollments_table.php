<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
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

            $table->string('roll_no', 50)
                ->nullable();

            /*
             * active / promoted / transferred /
             * withdrawn / completed
             */
            $table->string('enrollment_status', 30)
                ->default('active');

            $table->date('enrollment_date');

            $table->boolean('is_current')
                ->default(true);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['student_id', 'academic_year_id'],
                'student_year_unique'
            );

            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'school_class_id',
                    'section_id'
                ],
                'student_class_section_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};