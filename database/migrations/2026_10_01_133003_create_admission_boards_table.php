<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_boards', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->string('name', 150);
            $table->string('code', 50)->nullable();

            // Board / University
            $table->enum('type', ['board', 'university'])
                ->default('board');

            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('school_id', 'adm_board_school_fk')
                ->references('id')
                ->on('schools')
                ->restrictOnDelete();

            $table->foreign('created_by', 'adm_board_created_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique(
                ['school_id', 'name'],
                'adm_board_school_name_unique'
            );

            $table->index(
                ['school_id', 'status', 'sort_order'],
                'adm_board_list_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_boards');
    }
};