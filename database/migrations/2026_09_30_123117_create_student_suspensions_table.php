<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_suspensions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->cascadeOnDelete();

            $table->date('suspension_from');

            $table->date('suspension_to')
                ->nullable();

            $table->string('reason', 255);

            $table->text('remarks')
                ->nullable();

            $table->enum('status', [
                'active',
                'revoked',
                'completed'
            ])->default('active');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('revoked_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('revoked_at')
                ->nullable();

            $table->text('revocation_remarks')
                ->nullable();

            $table->timestamps();

            $table->index(
                [
                    'school_id',
                    'student_id',
                    'status'
                ],
                'student_susp_school_student_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_suspensions'
        );
    }
};