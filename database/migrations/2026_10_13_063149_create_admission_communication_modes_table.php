<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_communication_modes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('school_id');

            $table->string('name', 100);
            $table->string('code', 30)->nullable();

            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->foreign('school_id', 'adm_comm_school_fk')
                ->references('id')
                ->on('schools')
                ->restrictOnDelete();

            $table->foreign('created_by', 'adm_comm_created_fk')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unique(
                ['school_id', 'name'],
                'adm_comm_school_name_unique'
            );

            $table->index(
                ['school_id', 'status', 'sort_order'],
                'adm_comm_list_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_communication_modes');
    }
};