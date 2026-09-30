<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_collections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->string('receipt_no', 50);

            $table->date('payment_date');

            $table->decimal('total_amount', 12, 2);

            $table->enum('payment_mode', [
                'cash',
                'card',
                'upi',
                'bank_transfer',
                'cheque',
                'online',
                'other'
            ])->default('cash');

            $table->string('transaction_reference', 150)
                ->nullable();

            $table->string('bank_name', 150)
                ->nullable();

            $table->string('cheque_no', 100)
                ->nullable();

            $table->date('cheque_date')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->foreignId('collected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
             * posted   = valid payment
             * cancelled = reversed/cancelled receipt
             */
            $table->enum('status', [
                'posted',
                'cancelled'
            ])->default('posted');

            $table->foreignId('cancelled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->text('cancellation_reason')
                ->nullable();

            $table->timestamps();

            $table->unique(
                ['school_id', 'receipt_no'],
                'fee_collection_receipt_unique'
            );

            $table->index(
                [
                    'school_id',
                    'student_id',
                    'academic_year_id'
                ],
                'fee_collection_student_idx'
            );

            $table->index(
                [
                    'school_id',
                    'payment_date',
                    'status'
                ],
                'fee_collection_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_collections');
    }
};