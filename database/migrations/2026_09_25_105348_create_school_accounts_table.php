<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('bank_master_id')
                ->constrained('bank_masters')
                ->restrictOnDelete();

            $table->string('account_name', 150);

            $table->string('account_number', 100);

            $table->enum('account_type', [
                'savings',
                'current',
                'cash_credit',
                'overdraft',
                'other'
            ])->default('current');

            $table->string('purpose', 150)
                ->nullable();

            $table->string('upi_id', 150)
                ->nullable();

            $table->string('merchant_id', 150)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->boolean('is_default')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'account_number'],
                'school_account_number_unique'
            );

            $table->index(
                ['school_id', 'bank_master_id'],
                'school_account_bank_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_accounts');
    }
};