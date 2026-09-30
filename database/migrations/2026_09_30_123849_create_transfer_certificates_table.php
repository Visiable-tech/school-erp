<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_certificates', function (Blueprint $table) {

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

            $table->string('tc_number', 100);

            $table->date('application_date')->nullable();

            $table->date('issue_date');

            $table->date('leaving_date')->nullable();

            $table->foreignId('tc_reason_id')
                ->nullable()
                ->constrained('tc_reasons')
                ->nullOnDelete();

            $table->foreignId('tc_remark_option_id')
                ->nullable()
                ->constrained('tc_remark_options')
                ->nullOnDelete();

            $table->foreignId('tc_last_result_option_id')
                ->nullable()
                ->constrained('tc_last_result_options')
                ->nullOnDelete();

            $table->text('remarks')->nullable();

            $table->string('conduct', 150)->nullable();

            $table->string('next_school', 255)->nullable();

            $table->string('next_class', 100)->nullable();

            $table->boolean('fees_cleared')
                ->default(false);

            $table->boolean('library_cleared')
                ->default(false);

            $table->boolean('transport_cleared')
                ->default(false);

            $table->enum('status', [
                'draft',
                'issued',
                'cancelled'
            ])->default('draft');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('issued_at')->nullable();

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')->nullable();

            $table->text('cancellation_reason')->nullable();

            $table->timestamps();

            $table->unique(
                ['school_id', 'tc_number'],
                'tc_school_number_unique'
            );

            $table->index(
                ['school_id', 'student_id', 'status'],
                'tc_school_student_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_certificates');
    }
};