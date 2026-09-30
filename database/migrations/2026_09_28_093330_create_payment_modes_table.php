<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_modes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('name', 100);
            $table->string('code', 30)->nullable();

            $table->enum('mode_type', [
                'cash',
                'cheque',
                'dd',
                'card',
                'upi',
                'bank_transfer',
                'online',
                'other'
            ])->default('cash');

            $table->boolean('requires_reference')
                ->default(false);

            $table->boolean('requires_bank')
                ->default(false);

            $table->boolean('requires_instrument_date')
                ->default(false);

            $table->boolean('is_online')
                ->default(false);

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
                ['school_id', 'name'],
                'payment_mode_school_name_unique'
            );

            $table->unique(
                ['school_id', 'code'],
                'payment_mode_school_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_modes');
    }
};