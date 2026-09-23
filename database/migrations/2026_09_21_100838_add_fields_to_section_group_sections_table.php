<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('section_group_sections', function (Blueprint $table) {

            $table->foreignId('section_group_id')
                ->after('id')
                ->constrained('section_groups')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->after('section_group_id')
                ->constrained('sections')
                ->cascadeOnDelete();

            $table->unique(
                ['section_group_id', 'section_id'],
                'section_group_section_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('section_group_sections', function (Blueprint $table) {

            $table->dropUnique('section_group_section_unique');

            $table->dropForeign(['section_group_id']);
            $table->dropForeign(['section_id']);

            $table->dropColumn([
                'section_group_id',
                'section_id'
            ]);
        });
    }
};