<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_calendar_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->foreignId('calendar_event_type_id')
                ->nullable()
                ->constrained('calendar_event_types')
                ->nullOnDelete();

            $table->string('title');

            $table->text('description')->nullable();

            $table->dateTime('start_at');

            $table->dateTime('end_at')->nullable();

            $table->boolean('is_all_day')->default(false);

            $table->boolean('is_holiday')->default(false);

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_calendar_events');
    }
};