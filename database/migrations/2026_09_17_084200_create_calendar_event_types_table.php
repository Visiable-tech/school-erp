<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_event_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('code', 50)->nullable();

            $table->boolean('is_holiday')->default(false);

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'name'],
                'school_event_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_event_types');
    }
};