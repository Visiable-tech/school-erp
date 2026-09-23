<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_application_documents', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('admission_application_id')
                ->constrained('admission_applications')
                ->cascadeOnDelete();

            $table->string('document_type', 100);

            $table->string('document_name', 150);

            $table->string('document_number', 100)
                ->nullable();

            $table->string('file_path', 500)
                ->nullable();

            /*
             * pending / verified / rejected
             */
            $table->string('verification_status', 30)
                ->default('pending');

            $table->text('verification_remarks')
                ->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')
                ->nullable();

            $table->foreignId('uploaded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->index(
                [
                    'school_id',
                    'admission_application_id'
                ],
                'application_document_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'admission_application_documents'
        );
    }
};