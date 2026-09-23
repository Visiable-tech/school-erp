<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_followups', function (Blueprint $table) {

            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('admission_enquiry_id')
                ->constrained('admission_enquiries')
                ->cascadeOnDelete();

            $table->date('followup_date');

            $table->string('followup_type', 50)
                ->nullable();

            $table->string('contact_person', 150)
                ->nullable();

            $table->string('response_status', 50)
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->date('next_followup_date')
                ->nullable();

            $table->foreignId('followed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->boolean('status')
                ->default(true);

            $table->timestamps();

            $table->index(
                [
                    'school_id',
                    'admission_enquiry_id',
                    'followup_date'
                ],
                'admission_followup_idx'
            );

            $table->index(
                [
                    'school_id',
                    'next_followup_date'
                ],
                'admission_next_followup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_followups');
    }
};