<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotion_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->string('name', 100);
            $table->string('code', 50)->nullable();

            $table->enum('type', [
                'promoted',
                'repeated',
                'detained',
                'conditional',
                'other'
            ])->default('promoted');

            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'name'],
                'promotion_status_school_name_unique'
            );

            $table->index(
                ['school_id', 'status'],
                'promotion_status_school_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_statuses');
    }
};