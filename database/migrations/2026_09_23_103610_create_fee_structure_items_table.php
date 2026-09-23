<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structure_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('fee_structure_id')
                ->constrained('fee_structures')
                ->cascadeOnDelete();

            $table->foreignId('fee_head_id')
                ->constrained('fee_heads')
                ->restrictOnDelete();

            $table->decimal('amount', 12, 2);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['fee_structure_id', 'fee_head_id'],
                'fee_structure_head_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structure_items');
    }
};