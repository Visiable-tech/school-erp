<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('misc_fee_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->string('code', 50)
                ->nullable();

            /*
             * If fixed_amount = true,
             * default_amount is normally used.
             *
             * If false, amount can be entered
             * during collection/assignment.
             */
            $table->boolean('fixed_amount')
                ->default(true);

            $table->decimal('default_amount', 12, 2)
                ->nullable();

            $table->boolean('is_refundable')
                ->default(false);

            $table->boolean('allow_concession')
                ->default(false);

            $table->text('description')
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'name'],
                'misc_fee_comp_name_unique'
            );

            $table->unique(
                ['school_id', 'code'],
                'misc_fee_comp_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'misc_fee_components'
        );
    }
};