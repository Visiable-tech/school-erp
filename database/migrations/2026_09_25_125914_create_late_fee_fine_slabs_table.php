<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('late_fee_fine_slabs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('late_fee_fine_rule_id')
                ->constrained('late_fee_fine_rules')
                ->cascadeOnDelete();

            $table->enum('period_type', [
                'day_range',
                'after_first_month'
            ]);

            $table->unsignedTinyInteger('from_day')
                ->nullable();

            $table->unsignedTinyInteger('to_day')
                ->nullable();

            $table->decimal('amount', 12, 2)
                ->default(0);

            $table->boolean('per_month')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->index(
                'late_fee_fine_rule_id',
                'late_fine_slab_rule_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'late_fee_fine_slabs'
        );
    }
};