<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('section_groups', function (Blueprint $table) {

            $table->foreignId('school_id')
                ->after('id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->after('school_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->string('name', 150)
                ->after('academic_year_id');

            $table->boolean('status')
                ->default(true)
                ->after('name');

            $table->unique(
                [
                    'school_id',
                    'academic_year_id',
                    'name'
                ],
                'section_group_unique'
            );
        });
    }


    public function down(): void
    {
        Schema::table('section_groups', function (Blueprint $table) {

            $table->dropUnique('section_group_unique');

            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['school_id']);

            $table->dropColumn([
                'school_id',
                'academic_year_id',
                'name',
                'status'
            ]);
        });
    }
};