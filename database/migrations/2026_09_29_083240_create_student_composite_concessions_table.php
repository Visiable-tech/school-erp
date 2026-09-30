<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_composite_concessions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('student_enrollment_id');

            $table->date('assigned_date');

            // Regular concession
            $table->enum('concession_mode', [
                'none',
                'fixed',
                'percentage',
            ])->default('none');

            $table->decimal(
                'concession_value',
                12,
                2
            )->nullable();


            // Fee waiver
            $table->enum('fee_waiver_mode', [
                'none',
                'full',
                'fixed',
                'percentage',
            ])->default('none');

            $table->decimal(
                'fee_waiver_value',
                12,
                2
            )->nullable();


            // Fine waiver
            $table->enum('fine_waiver_mode', [
                'none',
                'full',
                'fixed',
                'percentage',
            ])->default('none');

            $table->decimal(
                'fine_waiver_value',
                12,
                2
            )->nullable();


            $table->json(
                'selected_due_ids'
            )->nullable();

            $table->text('reason');

            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->unsignedBigInteger(
                'assigned_by'
            )->nullable();

            $table->unsignedBigInteger(
                'approved_by'
            )->nullable();

            $table->timestamp(
                'approved_at'
            )->nullable();

            $table->boolean(
                'status'
            )->default(true);

            $table->timestamps();


            $table->foreign(
                'school_id',
                'scc_school_fk'
            )
            ->references('id')
            ->on('schools')
            ->restrictOnDelete();


            $table->foreign(
                'academic_year_id',
                'scc_year_fk'
            )
            ->references('id')
            ->on('academic_years')
            ->restrictOnDelete();


            $table->foreign(
                'student_id',
                'scc_student_fk'
            )
            ->references('id')
            ->on('students')
            ->restrictOnDelete();


            $table->foreign(
                'student_enrollment_id',
                'scc_enroll_fk'
            )
            ->references('id')
            ->on('student_enrollments')
            ->restrictOnDelete();


            $table->foreign(
                'assigned_by',
                'scc_assigned_fk'
            )
            ->references('id')
            ->on('users')
            ->nullOnDelete();


            $table->foreign(
                'approved_by',
                'scc_approved_fk'
            )
            ->references('id')
            ->on('users')
            ->nullOnDelete();


            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'student_id',
                ],
                'scc_student_idx'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'student_composite_concessions'
        );
    }
};