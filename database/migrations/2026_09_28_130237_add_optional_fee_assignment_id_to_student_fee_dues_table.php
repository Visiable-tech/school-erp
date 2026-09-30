<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'student_fee_dues',
            function (Blueprint $table) {

                $table->foreignId(
                    'student_optional_fee_assignment_id'
                )
                ->nullable()
                ->after('student_fee_assignment_id')
                ->constrained(
                    'student_optional_fee_assignments'
                )
                ->restrictOnDelete();

                $table->index(
                    'student_optional_fee_assignment_id',
                    'student_due_optional_fee_idx'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'student_fee_dues',
            function (Blueprint $table) {

                $table->dropForeign([
                    'student_optional_fee_assignment_id'
                ]);

                $table->dropIndex(
                    'student_due_optional_fee_idx'
                );

                $table->dropColumn(
                    'student_optional_fee_assignment_id'
                );
            }
        );
    }
};