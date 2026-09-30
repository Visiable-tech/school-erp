<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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

            $table->enum('fine_type', [
                'fixed',
                'per_day',
                'percentage'
            ]);

            $table->unsignedInteger('grace_days')
                ->default(0);

            $table->decimal('fine_value', 12, 2)
                ->default(0);

            $table->decimal('maximum_fine', 12, 2)
                ->nullable();

            $table->boolean('apply_on_outstanding')
                ->default(true);

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
                'late_fine_school_code_unique'
            );

            $table->index(
                ['school_id', 'academic_year_id'],
                'late_fine_school_year_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'late_fee_fine_rules'
        );
    }
};