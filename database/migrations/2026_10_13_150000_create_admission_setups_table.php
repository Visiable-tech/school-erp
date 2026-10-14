<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admission_setups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
            $table->string('admission_code', 100);
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->unsignedInteger('no_of_seats')->default(100);
            $table->unsignedSmallInteger('required_age_months')->nullable();
            $table->date('dob_check_date')->nullable();
            $table->date('dob_check_start')->nullable();
            $table->date('dob_check_end')->nullable();
            $table->date('opens_on')->nullable();
            $table->date('closes_on')->nullable();
            $table->dateTime('default_exam_at')->nullable();
            $table->string('syllabus_path')->nullable();
            $table->decimal('form_fee', 12, 2)->default(0);
            $table->string('form_fee_label', 150)->nullable();
            $table->decimal('registration_fee', 12, 2)->default(0);
            $table->string('registration_fee_label', 150)->nullable();
            $table->decimal('online_registration_fee', 12, 2)->default(0);
            // Future masters: document group, exam group, accounting ledger, email templates.
            $table->timestamps();
            $table->unique(['school_id', 'academic_year_id', 'school_class_id'], 'adm_setup_school_year_class_unique');
            $table->unique(['school_id', 'admission_code'], 'adm_setup_school_code_unique');
        });
    }
    public function down(): void { Schema::dropIfExists('admission_setups'); }
};
