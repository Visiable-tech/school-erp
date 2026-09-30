<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_waiver_assignment_dues', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger(
                'student_fee_waiver_assignment_id'
            );

            $table->unsignedBigInteger(
                'student_fee_due_id'
            );

            $table->decimal(
                'waiver_amount',
                12,
                2
            );

            $table->timestamps();

            $table->foreign(
                'student_fee_waiver_assignment_id',
                'sfwa_due_assignment_fk'
            )
            ->references('id')
            ->on('student_fee_waiver_assignments')
            ->cascadeOnDelete();

            $table->foreign(
                'student_fee_due_id',
                'sfwa_due_fee_due_fk'
            )
            ->references('id')
            ->on('student_fee_dues')
            ->restrictOnDelete();

            $table->unique(
                [
                    'student_fee_waiver_assignment_id',
                    'student_fee_due_id'
                ],
                'sfwa_due_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_fee_waiver_assignment_dues'
        );
    }
};