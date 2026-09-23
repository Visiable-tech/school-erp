<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_enquiries', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('admission_session_id')
                ->constrained('admission_sessions')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            /*
             * Class the parent is enquiring for.
             */
            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            /*
             * Enquiry identification
             */
            $table->string('enquiry_no', 50);

            $table->date('enquiry_date');

            /*
             * Student / Child
             */
            $table->string('student_name', 150);

            $table->date('date_of_birth')
                ->nullable();

            $table->string('gender', 20)
                ->nullable();

            /*
             * Parent / Guardian
             */
            $table->string('father_name', 150)
                ->nullable();

            $table->string('mother_name', 150)
                ->nullable();

            $table->string('guardian_name', 150)
                ->nullable();

            $table->string('mobile', 20);

            $table->string('alternate_mobile', 20)
                ->nullable();

            $table->string('email', 150)
                ->nullable();

            /*
             * Address
             */
            $table->text('address')
                ->nullable();

            $table->string('city', 100)
                ->nullable();

            $table->string('pin_code', 10)
                ->nullable();

            /*
             * Previous School
             */
            $table->string('previous_school', 200)
                ->nullable();

            /*
             * Lead information
             */
            $table->string('source', 100)
                ->nullable();

            $table->string('reference_name', 150)
                ->nullable();

            /*
             * Admission enquiry status
             *
             * open
             * follow_up
             * interested
             * not_interested
             * applied
             * admitted
             * closed
             */
            $table->string('enquiry_status', 30)
                ->default('open');

            $table->date('next_followup_date')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            /*
             * User who created enquiry.
             */
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'enquiry_no'],
                'school_enquiry_no_unique'
            );

            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'enquiry_status'
                ],
                'admission_enquiry_status_idx'
            );

            $table->index(
                ['school_id', 'mobile'],
                'admission_enquiry_mobile_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_enquiries');
    }
};