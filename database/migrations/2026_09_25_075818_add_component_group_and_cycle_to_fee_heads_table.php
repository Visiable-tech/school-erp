<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_heads', function (Blueprint $table) {

            $table->foreignId('fee_component_group_id')
                ->nullable()
                ->after('school_id')
                ->constrained('fee_component_groups')
                ->restrictOnDelete();

            $table->foreignId('fee_cycle_id')
                ->nullable()
                ->after('fee_component_group_id')
                ->constrained('fee_cycles')
                ->restrictOnDelete();

            $table->boolean('is_refundable')
                ->default(false)
                ->after('is_optional');

            $table->boolean('allow_concession')
                ->default(true)
                ->after('is_refundable');

            $table->boolean('allow_waiver')
                ->default(true)
                ->after('allow_concession');

            $table->text('description')
                ->nullable()
                ->after('allow_waiver');

            $table->index(
                ['school_id', 'fee_component_group_id'],
                'fee_head_group_idx'
            );

            $table->index(
                ['school_id', 'fee_cycle_id'],
                'fee_head_cycle_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('fee_heads', function (Blueprint $table) {

            $table->dropIndex('fee_head_group_idx');
            $table->dropIndex('fee_head_cycle_idx');

            $table->dropForeign([
                'fee_component_group_id'
            ]);

            $table->dropForeign([
                'fee_cycle_id'
            ]);

            $table->dropColumn([
                'fee_component_group_id',
                'fee_cycle_id',
                'is_refundable',
                'allow_concession',
                'allow_waiver',
                'description',
            ]);
        });
    }
};