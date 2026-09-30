<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_dues', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('student_fee_assignment_id')
                ->constrained('student_fee_assignments')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('student_enrollment_id')
                ->constrained('student_enrollments')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('fee_structure_id')
                ->constrained('fee_structures')
                ->restrictOnDelete();

            $table->foreignId('fee_structure_item_id')
                ->nullable()
                ->constrained('fee_structure_items')
                ->nullOnDelete();

            $table->foreignId('fee_installment_id')
                ->nullable()
                ->constrained('fee_installments')
                ->nullOnDelete();

            $table->foreignId('fee_head_id')
                ->constrained('fee_heads')
                ->restrictOnDelete();

            /*
             * Snapshot fields
             * These values should not change when master data changes.
             */
            $table->string('fee_head_name', 150);
            $table->string('installment_name', 100);

            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->date('due_date');

            $table->decimal('base_amount', 12, 2);

            $table->decimal('discount_amount', 12, 2)->default(0);

            $table->decimal('fine_amount', 12, 2)->default(0);

            $table->decimal('payable_amount', 12, 2);

            $table->decimal('paid_amount', 12, 2)->default(0);

            $table->decimal('balance_amount', 12, 2);

            $table->enum('payment_status', [
                'unpaid',
                'partial',
                'paid'
            ])->default('unpaid');

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                [
                    'student_fee_assignment_id',
                    'fee_installment_id'
                ],
                'student_fee_due_installment_unique'
            );

            $table->index(
                ['school_id', 'student_id', 'academic_year_id'],
                'student_fee_due_student_idx'
            );

            $table->index(
                ['school_id', 'due_date', 'payment_status'],
                'student_fee_due_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_dues');
    }
};