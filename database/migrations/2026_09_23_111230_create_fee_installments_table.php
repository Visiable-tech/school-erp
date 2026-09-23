<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_installments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('fee_structure_id')
                ->constrained('fee_structures')
                ->cascadeOnDelete();

            $table->foreignId('fee_structure_item_id')
                ->constrained('fee_structure_items')
                ->cascadeOnDelete();

            $table->string('installment_name', 100);

            $table->date('period_start')->nullable();

            $table->date('period_end')->nullable();

            $table->date('due_date');

            $table->decimal('amount', 12, 2);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                [
                    'fee_structure_item_id',
                    'installment_name'
                ],
                'fee_item_installment_unique'
            );

            $table->index(
                [
                    'school_id',
                    'fee_structure_id',
                    'due_date'
                ],
                'fee_installment_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_installments');
    }
};