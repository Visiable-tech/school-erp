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

                $table->foreignId('promotion_status_id')
                    ->nullable()
                    ->after('student_type_id')
                    ->constrained('promotion_statuses')
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
                    'promotion_status_id'
                ]);

                $table->dropColumn(
                    'promotion_status_id'
                );

            }
        );
    }
};