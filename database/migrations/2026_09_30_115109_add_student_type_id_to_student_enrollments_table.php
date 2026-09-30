<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'student_enrollments',
            function (Blueprint $table) {

                $table->foreignId('student_type_id')
                    ->nullable()
                    ->after('section_id')
                    ->constrained('student_types')
                    ->nullOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'student_enrollments',
            function (Blueprint $table) {

                $table->dropForeign([
                    'student_type_id'
                ]);

                $table->dropColumn(
                    'student_type_id'
                );
            }
        );
    }
};