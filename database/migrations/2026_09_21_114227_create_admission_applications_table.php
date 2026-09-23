<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_applications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('admission_enquiry_id')
                ->constrained('admission_enquiries')
                ->restrictOnDelete();

            $table->foreignId('admission_session_id')
                ->constrained('admission_sessions')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->restrictOnDelete();

            $table->string('application_no', 50);

            $table->date('application_date');

            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            $table->string('student_name', 150);

            $table->date('date_of_birth')->nullable();

            $table->string('gender', 20)->nullable();

            $table->string('blood_group', 10)->nullable();

            $table->string('nationality', 100)
                ->default('Indian');

            $table->string('religion', 100)->nullable();

            $table->string('category', 50)->nullable();

            $table->string('mother_tongue', 100)->nullable();

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

            /*
            |--------------------------------------------------------------------------
            | Application Workflow
            |--------------------------------------------------------------------------
            |
            | draft
            | submitted
            | under_review
            | approved
            | rejected
            | admitted
            |
            */

            $table->string('application_status', 30)
                ->default('draft');

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            /*
             * One enquiry = one application
             */
            $table->unique(
                ['school_id', 'admission_enquiry_id'],
                'admission_enquiry_application_unique'
            );

            $table->unique(
                ['school_id', 'application_no'],
                'school_application_no_unique'
            );

            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'application_status'
                ],
                'admission_application_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};