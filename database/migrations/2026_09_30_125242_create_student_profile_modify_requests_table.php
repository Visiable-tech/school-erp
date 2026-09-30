<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profile_modify_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->date('request_date');

            $table->json('requested_changes');

            $table->text('request_remarks')
                ->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->foreignId('requested_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable();

            $table->text('review_remarks')
                ->nullable();

            $table->timestamps();

            $table->index(
                ['school_id', 'status'],
                'profile_modify_school_status_idx'
            );

            $table->index(
                ['student_id', 'status'],
                'profile_modify_student_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_profile_modify_requests'
        );
    }
};