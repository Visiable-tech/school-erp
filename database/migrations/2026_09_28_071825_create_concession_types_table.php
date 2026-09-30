<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concession_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('name', 150);
            $table->string('code', 50)->nullable();

            $table->enum('concession_mode', [
                'fixed',
                'percentage'
            ])->default('percentage');

            $table->decimal('default_value', 12, 2)
                ->nullable();

            $table->decimal('maximum_amount', 12, 2)
                ->nullable();

            $table->boolean('requires_approval')
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
                'concession_type_school_name_unique'
            );

            $table->unique(
                ['school_id', 'code'],
                'concession_type_school_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concession_types');
    }
};