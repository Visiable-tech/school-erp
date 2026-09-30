<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_refunds', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('student_enrollment_id');
            $table->unsignedBigInteger('fee_collection_id');

            $table->string('refund_no', 100);
            $table->date('refund_date');

            $table->decimal('total_amount', 12, 2)
                ->default(0);

            $table->text('reason')->nullable();

            $table->unsignedBigInteger('processed_by')
                ->nullable();

            $table->string('status', 30)
                ->default('posted');

            $table->text('cancellation_reason')
                ->nullable();

            $table->unsignedBigInteger('cancelled_by')
                ->nullable();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'school_id',
                'fr_school_fk'
            )
                ->references('id')
                ->on('schools')
                ->restrictOnDelete();


            $table->foreign(
                'academic_year_id',
                'fr_year_fk'
            )
                ->references('id')
                ->on('academic_years')
                ->restrictOnDelete();


            $table->foreign(
                'student_id',
                'fr_student_fk'
            )
                ->references('id')
                ->on('students')
                ->restrictOnDelete();


            $table->foreign(
                'student_enrollment_id',
                'fr_enrollment_fk'
            )
                ->references('id')
                ->on('student_enrollments')
                ->restrictOnDelete();


            $table->foreign(
                'fee_collection_id',
                'fr_collection_fk'
            )
                ->references('id')
                ->on('fee_collections')
                ->restrictOnDelete();


            $table->foreign(
                'processed_by',
                'fr_processed_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();


            $table->foreign(
                'cancelled_by',
                'fr_cancelled_by_fk'
            )
                ->references('id')
                ->on('users')
                ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Unique / Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['school_id', 'refund_no'],
                'fr_school_refund_no_unique'
            );

            $table->index(
                ['school_id', 'refund_date'],
                'fr_school_date_idx'
            );

            $table->index(
                ['student_id', 'status'],
                'fr_student_status_idx'
            );

            $table->index(
                'fee_collection_id',
                'fr_collection_idx'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('fee_refunds');
    }
};