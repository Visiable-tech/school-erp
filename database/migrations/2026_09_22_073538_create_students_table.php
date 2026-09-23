<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('admission_application_id')
                ->nullable()
                ->constrained('admission_applications')
                ->restrictOnDelete();

            $table->string('admission_no', 50);

            $table->date('admission_date');

            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            $table->string('student_name', 150);

            $table->date('date_of_birth')->nullable();

            $table->string('gender', 20)->nullable();

            $table->string('blood_group', 10)->nullable();

            $table->string('nationality', 100)->nullable();

            $table->string('religion', 100)->nullable();

            $table->string('category', 50)->nullable();

            $table->string('mother_tongue', 100)->nullable();

            $table->string('student_photo', 500)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Father
            |--------------------------------------------------------------------------
            */

            $table->string('father_name', 150)->nullable();

            $table->string('father_mobile', 20)->nullable();

            $table->string('father_email', 150)->nullable();

            $table->string('father_occupation', 150)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Mother
            |--------------------------------------------------------------------------
            */

            $table->string('mother_name', 150)->nullable();

            $table->string('mother_mobile', 20)->nullable();

            $table->string('mother_email', 150)->nullable();

            $table->string('mother_occupation', 150)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Guardian
            |--------------------------------------------------------------------------
            */

            $table->string('guardian_name', 150)->nullable();

            $table->string('guardian_relation', 100)->nullable();

            $table->string('guardian_mobile', 20)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Address
            |--------------------------------------------------------------------------
            */

            $table->text('present_address')->nullable();

            $table->string('present_city', 100)->nullable();

            $table->string('present_state', 100)->nullable();

            $table->string('present_pin_code', 10)->nullable();

            $table->text('permanent_address')->nullable();

            $table->string('permanent_city', 100)->nullable();

            $table->string('permanent_state', 100)->nullable();

            $table->string('permanent_pin_code', 10)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Previous School
            |--------------------------------------------------------------------------
            */

            $table->string('previous_school', 200)->nullable();

            $table->string('previous_class', 100)->nullable();

            $table->string('previous_board', 100)->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'admission_no'],
                'school_admission_no_unique'
            );

            /*
             * Prevent one application creating multiple students.
             */
            $table->unique(
                'admission_application_id',
                'student_application_unique'
            );

            $table->index(
                ['school_id', 'student_name'],
                'student_name_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};