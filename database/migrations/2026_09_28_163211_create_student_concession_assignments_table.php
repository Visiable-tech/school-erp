<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_concession_assignments', function (Blueprint $table) {

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

            $table->foreignId('concession_type_id')
                ->constrained('concession_types')
                ->restrictOnDelete();

            $table->enum('concession_mode', [
                'fixed',
                'percentage'
            ]);

            $table->decimal('concession_value', 12, 2);

            $table->decimal('maximum_amount', 12, 2)
                ->nullable();

            $table->date('assigned_date');

            $table->date('effective_from')
                ->nullable();

            $table->date('effective_to')
                ->nullable();

            $table->enum('approval_status', [
                'not_required',
                'pending',
                'approved',
                'rejected'
            ])->default('not_required');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable();

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
            ], 'student_concession_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_concession_assignments'
        );
    }
};