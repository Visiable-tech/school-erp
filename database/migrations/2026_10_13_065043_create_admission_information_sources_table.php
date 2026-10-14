<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_information_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();

            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('status')->default(true);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['school_id', 'name'], 'adm_source_school_name_unique');
            $table->index(['school_id', 'status', 'sort_order'], 'adm_source_list_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_information_sources');
    }
};