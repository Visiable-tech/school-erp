<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_component_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->string('code', 50)
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'name'],
                'fee_comp_group_name_unique'
            );

            $table->unique(
                ['school_id', 'code'],
                'fee_comp_group_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_component_groups');
    }
};