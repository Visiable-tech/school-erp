<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_receipt_schemes', function (Blueprint $table) {
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

            $table->string('prefix', 50)
                ->nullable();

            $table->string('suffix', 50)
                ->nullable();

            $table->string('separator', 5)
                ->default('/');

            $table->boolean('include_academic_year')
                ->default(true);

            $table->unsignedTinyInteger('number_length')
                ->default(6);

            $table->unsignedBigInteger('start_number')
                ->default(1);

            /*
             * 0 means no receipt generated yet.
             * We will update this only when
             * an actual receipt is posted.
             */
            $table->unsignedBigInteger('last_number')
                ->default(0);

            $table->boolean('reset_yearly')
                ->default(true);

            $table->boolean('is_default')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'code'],
                'fee_receipt_scheme_code_unique'
            );

            $table->index(
                ['school_id', 'academic_year_id'],
                'fee_receipt_scheme_year_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'fee_receipt_schemes'
        );
    }
};