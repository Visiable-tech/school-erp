<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_cycles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('name', 100);

            $table->string('code', 30)
                ->nullable();

            /*
             * Number of times the fee normally occurs
             * during one academic year.
             *
             * Monthly       = 12
             * Quarterly     = 4
             * Half Yearly   = 2
             * Annual        = 1
             * One Time      = 1
             *
             * Custom cycles can also be created.
             */
            $table->unsignedSmallInteger('installments_count')
                ->default(1);

            /*
             * Used later by Fee Template / Compile Fee.
             */
            $table->enum('cycle_type', [
                'monthly',
                'quarterly',
                'half_yearly',
                'annual',
                'one_time',
                'custom'
            ])->default('custom');

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'name'],
                'fee_cycle_school_name_unique'
            );

            $table->unique(
                ['school_id', 'code'],
                'fee_cycle_school_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_cycles');
    }
};