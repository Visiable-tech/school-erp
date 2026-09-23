<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->text('description')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                [
                    'school_id',
                    'academic_year_id',
                    'school_class_id',
                    'name'
                ],
                'fee_structure_unique'
            );

            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'school_class_id'
                ],
                'fee_structure_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};