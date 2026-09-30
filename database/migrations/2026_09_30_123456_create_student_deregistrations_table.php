<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_deregistrations', function (Blueprint $table) {

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

            $table->date('deregistration_date');

            $table->string('reason', 255);

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['school_id', 'student_id'],
                'student_dereg_school_student_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_deregistrations');
    }
};