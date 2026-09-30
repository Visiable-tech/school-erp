<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'student_fine_waiver_assignment_dues',
            function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger(
                    'student_fine_waiver_assignment_id'
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
                    'student_fine_waiver_assignment_id',
                    'sfwad_assignment_fk'
                )
                    ->references('id')
                    ->on('student_fine_waiver_assignments')
                    ->cascadeOnDelete();


                $table->foreign(
                    'student_fee_due_id',
                    'sfwad_due_fk'
                )
                    ->references('id')
                    ->on('student_fee_dues')
                    ->restrictOnDelete();


                $table->unique(
                    [
                        'student_fine_waiver_assignment_id',
                        'student_fee_due_id'
                    ],
                    'sfwad_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'student_fine_waiver_assignment_dues'
        );
    }
};