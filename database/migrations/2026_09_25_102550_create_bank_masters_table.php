<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_masters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('bank_name', 150);
            $table->string('short_name', 50)->nullable();
            $table->string('bank_code', 50)->nullable();

            $table->string('branch_name', 150)->nullable();
            $table->string('branch_code', 50)->nullable();

            $table->string('ifsc_code', 20)->nullable();
            $table->string('micr_code', 20)->nullable();

            $table->text('address')->nullable();

            $table->string('city', 100)->nullable();

            $table->string('contact_person', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'bank_code'],
                'bank_master_school_code_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_masters');
    }
};