<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_compile_queues', function (Blueprint $table) {
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

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('sections')
                ->restrictOnDelete();

            $table->foreignId('fee_structure_id')
                ->constrained('fee_structures')
                ->restrictOnDelete();

            $table->date('compile_date');

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'cancelled'
            ])->default('pending');

            $table->unsignedInteger('total_students')
                ->default(0);

            $table->unsignedInteger('processed_students')
                ->default(0);

            $table->unsignedInteger('compiled_students')
                ->default(0);

            $table->unsignedInteger('skipped_students')
                ->default(0);

            $table->unsignedInteger('failed_students')
                ->default(0);

            $table->unsignedTinyInteger('progress')
                ->default(0);

            /*
             * Selected enrollment IDs.
             */
            $table->json('enrollment_ids')
                ->nullable();

            /*
             * Store errors/statistics without
             * creating another table yet.
             */
            $table->json('error_details')
                ->nullable();

            $table->text('error_message')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['school_id', 'status'],
                'fee_compile_queue_school_status_idx'
            );

            $table->index(
                [
                    'school_id',
                    'academic_year_id',
                    'school_class_id'
                ],
                'fee_compile_queue_scope_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'fee_compile_queues'
        );
    }
};