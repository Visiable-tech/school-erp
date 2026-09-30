<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('late_fee_fine_rules')) {

            Schema::create('late_fee_fine_rules', function (Blueprint $table) {
                $table->id();

                $table->foreignId('school_id')
                    ->constrained('schools')
                    ->restrictOnDelete();

                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->constrained('academic_years')
                    ->restrictOnDelete();

                $table->string('name', 150);

                $table->string('code', 50)
                    ->nullable();

                $table->boolean('is_default')
                    ->default(false);

                $table->text('description')
                    ->nullable();

                $table->unsignedInteger('sort_order')
                    ->default(0);

                $table->boolean('status')
                    ->default(true);

                $table->timestamps();

                $table->unique(
                    ['school_id', 'code'],
                    'late_fine_rule_school_code_unique'
                );

                $table->index(
                    ['school_id', 'academic_year_id'],
                    'late_fine_rule_school_year_idx'
                );
            });
        }


        if (!Schema::hasTable('late_fee_fine_slabs')) {

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
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'late_fee_fine_slabs'
        );

        Schema::dropIfExists(
            'late_fee_fine_rules'
        );
    }
};