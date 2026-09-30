<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_certificates', function (Blueprint $table) {

            $table->foreignId('student_id')
                ->nullable()
                ->change();

            $table->foreignId('student_enrollment_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('transfer_certificates', function (Blueprint $table) {

            $table->foreignId('student_id')
                ->nullable(false)
                ->change();

            $table->foreignId('student_enrollment_id')
                ->nullable(false)
                ->change();
        });
    }
};