<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fine_waiver_assignments', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('student_enrollment_id');

            $table->date('assigned_date');

            $table->enum('waiver_mode', [
                'full',
                'fixed',
                'percentage',
            ])->default('full');

            $table->decimal('waiver_value', 12, 2)
                ->nullable();

            $table->json('selected_due_ids')->nullable();

            $table->text('reason')->nullable();

            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamp('approved_at')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();


            $table->foreign('school_id', 'sfwa_fine_school_fk')
                ->references('id')
                ->on('schools')
                ->restrictOnDelete();

            $table->foreign('academic_year_id', 'sfwa_fine_year_fk')
                ->references('id')
                ->on('academic_years')
                ->restrictOnDelete();

            $table->foreign('student_id', 'sfwa_fine_student_fk')
                ->references('id')
                ->on('students')
                ->restrictOnDelete();

            $table->foreign(
                'student_enrollment_id',
                'sfwa_fine_enroll_fk'
            )
                ->references('id')
                ->on('student_enrollments')
                ->restrictOnDelete();

            $table->foreign(
                'assigned_by',
                'sfwa_fine_assign_user_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign(
                'approved_by',
                'sfwa_fine_approve_user_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();


            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'student_id'
                ],
                'sfwa_fine_student_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_fine_waiver_assignments'
        );
    }
};