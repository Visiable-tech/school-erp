<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'student_composite_concession_dues',
            function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger(
                    'student_composite_concession_id'
                );

                $table->unsignedBigInteger(
                    'student_fee_due_id'
                );


                $table->decimal(
                    'concession_amount',
                    12,
                    2
                )->default(0);


                $table->decimal(
                    'fee_waiver_amount',
                    12,
                    2
                )->default(0);


                $table->decimal(
                    'fine_waiver_amount',
                    12,
                    2
                )->default(0);


                $table->timestamps();


                $table->foreign(
                    'student_composite_concession_id',
                    'sccd_composite_fk'
                )
                ->references('id')
                ->on('student_composite_concessions')
                ->cascadeOnDelete();


                $table->foreign(
                    'student_fee_due_id',
                    'sccd_due_fk'
                )
                ->references('id')
                ->on('student_fee_dues')
                ->restrictOnDelete();


                $table->unique(
                    [
                        'student_composite_concession_id',
                        'student_fee_due_id'
                    ],
                    'sccd_unique'
                );
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'student_composite_concession_dues'
        );
    }
};