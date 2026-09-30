<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'student_concession_assignments',
            function (Blueprint $table) {

                $table->json('selected_due_ids')
                    ->nullable()
                    ->after('effective_to');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'student_concession_assignments',
            function (Blueprint $table) {

                $table->dropColumn(
                    'selected_due_ids'
                );
            }
        );
    }
};