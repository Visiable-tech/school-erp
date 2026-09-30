<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheque_bounce_reasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->string('code', 50)
                ->nullable();

            $table->decimal('bounce_charge', 12, 2)
                ->default(0);

            $table->boolean('apply_charge')
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
                'cheque_bounce_school_name_unique'
            );

            $table->unique(
                ['school_id', 'code'],
                'cheque_bounce_school_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'cheque_bounce_reasons'
        );
    }
};