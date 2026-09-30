<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tc_last_result_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'name'],
                'tc_last_result_school_name_unique'
            );

            $table->index(
                ['school_id', 'status'],
                'tc_last_result_school_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tc_last_result_options');
    }
};