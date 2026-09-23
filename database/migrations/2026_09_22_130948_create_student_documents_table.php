<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->string('document_type', 100);

            $table->string('document_name', 200);

            $table->string('document_number', 100)
                ->nullable();

            $table->string('file_path');

            $table->text('remarks')
                ->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->index(
                ['school_id', 'student_id'],
                'student_document_student_idx'
            );

            $table->index(
                ['school_id', 'document_type'],
                'student_document_type_idx'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};