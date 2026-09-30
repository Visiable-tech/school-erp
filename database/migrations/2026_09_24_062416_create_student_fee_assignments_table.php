<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('fee_structure_id')
                ->constrained('fee_structures')
                ->restrictOnDelete();

            $table->date('assigned_date');

            $table->enum('discount_type', [
                'none',
                'fixed',
                'percentage'
            ])->default('none');

            $table->decimal('discount_value', 12, 2)->default(0);

            $table->text('remarks')->nullable();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['student_id', 'academic_year_id'],
                'student_fee_assignment_unique'
            );

            $table->index(
                ['school_id', 'academic_year_id', 'fee_structure_id'],
                'student_fee_assignment_lookup'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_assignments');
    }
};