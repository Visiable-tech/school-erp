<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_collection_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('fee_collection_id')
                ->constrained('fee_collections')
                ->cascadeOnDelete();

            $table->foreignId('student_fee_due_id')
                ->constrained('student_fee_dues')
                ->restrictOnDelete();

            /*
             * Amount allocated from this receipt
             * against this particular due.
             */
            $table->decimal('amount', 12, 2);

            /*
             * Snapshot fields for receipt history.
             */
            $table->string('fee_head_name', 150);

            $table->string('installment_name', 100);

            $table->date('due_date');

            $table->decimal('due_amount', 12, 2);

            $table->timestamps();

            $table->unique(
                [
                    'fee_collection_id',
                    'student_fee_due_id'
                ],
                'fee_collection_due_unique'
            );

            $table->index(
                [
                    'school_id',
                    'student_fee_due_id'
                ],
                'fee_collection_due_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_collection_items');
    }
};