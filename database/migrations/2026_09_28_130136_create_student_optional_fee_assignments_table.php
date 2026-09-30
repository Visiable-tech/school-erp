<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'student_optional_fee_assignments',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->restrictOnDelete();

                $table->foreignId('academic_year_id')
                    ->constrained('academic_years')
                    ->restrictOnDelete();

                $table->foreignId('student_id')
                    ->constrained('students')
                    ->restrictOnDelete();

                $table->foreignId('student_enrollment_id')
                    ->constrained('student_enrollments')
                    ->restrictOnDelete();

                $table->foreignId('fee_head_id')
                    ->constrained('fee_heads')
                    ->restrictOnDelete();

                $table->date('assigned_date');

                $table->date('effective_from')
                    ->nullable();

                $table->date('effective_to')
                    ->nullable();

                $table->decimal(
                    'amount',
                    12,
                    2
                );

                $table->text('remarks')
                    ->nullable();

                $table->foreignId('assigned_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->boolean('status')
                    ->default(true);

                $table->timestamps();

                $table->index([
                    'school_id',
                    'academic_year_id',
                    'student_id'
                ], 'optional_fee_student_idx');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_optional_fee_assignments'
        );
    }
};