<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('late_fee_fine_rules', function (Blueprint $table) {

            if (!Schema::hasColumn('late_fee_fine_rules', 'school_id')) {
                $table->foreignId('school_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('schools')
                    ->restrictOnDelete();
            }

            if (!Schema::hasColumn('late_fee_fine_rules', 'academic_year_id')) {
                $table->foreignId('academic_year_id')
                    ->nullable()
                    ->after('school_id')
                    ->constrained('academic_years')
                    ->restrictOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('late_fee_fine_rules', function (Blueprint $table) {

            if (Schema::hasColumn('late_fee_fine_rules', 'academic_year_id')) {
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }

            if (Schema::hasColumn('late_fee_fine_rules', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });
    }
};