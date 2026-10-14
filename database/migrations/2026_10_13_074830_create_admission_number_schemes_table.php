<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_number_schemes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->string('scheme_type', 50);
            $table->string('scheme_name', 100);

            $table->boolean('auto_generate')->default(true);
            $table->boolean('include_academic_year')->default(false);
            $table->boolean('include_class')->default(false);

            $table->string('separator', 5)->default('/');
            $table->string('fixed_string', 50)->nullable();

            $table->unsignedBigInteger('starting_number')->default(1);
            $table->unsignedBigInteger('last_generated_number')->default(0);
            $table->unsignedTinyInteger('number_length')->default(5);

            $table->boolean('allow_manual_entry')->default(true);
            $table->boolean('status')->default(true);

            $table->timestamps();

            $table->unique(
                ['school_id', 'scheme_type'],
                'adm_num_scheme_school_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_number_schemes');
    }
};