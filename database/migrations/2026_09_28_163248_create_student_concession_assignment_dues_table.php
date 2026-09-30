<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'student_concession_assignment_dues',
            function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger(
                    'student_concession_assignment_id'
                );

                $table->unsignedBigInteger(
                    'student_fee_due_id'
                );

                $table->decimal(
                    'concession_amount',
                    12,
                    2
                );

                $table->timestamps();


                // Short FK name
                $table->foreign(
                    'student_concession_assignment_id',
                    'sca_due_assignment_fk'
                )
                ->references('id')
                ->on('student_concession_assignments')
                ->cascadeOnDelete();


                // Short FK name
                $table->foreign(
                    'student_fee_due_id',
                    'sca_due_fee_due_fk'
                )
                ->references('id')
                ->on('student_fee_dues')
                ->restrictOnDelete();


                // Prevent same due being added twice
                $table->unique(
                    [
                        'student_concession_assignment_id',
                        'student_fee_due_id'
                    ],
                    'sca_due_unique'
                );


                $table->index(
                    'student_fee_due_id',
                    'sca_fee_due_idx'
                );
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'student_concession_assignment_dues'
        );
    }
};