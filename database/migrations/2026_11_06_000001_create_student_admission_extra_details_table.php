<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('student_admission_extra_details', function(Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->string('emergency_contact_name',255)->nullable();
            $table->string('emergency_contact_mobile',255)->nullable();
            $table->string('emergency_contact_relation',255)->nullable();
            $table->string('student_aadhaar_last4',4)->nullable();
            $table->string('birth_place',255)->nullable();
            $table->string('identification_mark',255)->nullable();
            $table->string('medical_allergies',255)->nullable();
            $table->string('medical_conditions',255)->nullable();
            $table->string('transport_required',255)->nullable();
            $table->string('pickup_person_name',255)->nullable();
            $table->string('pickup_person_mobile',255)->nullable();
            $table->string('father_aadhaar_last4', 4)->nullable();
            $table->string('father_nationality', 255)->nullable();
            $table->string('father_qualification', 255)->nullable();
            $table->string('father_organization', 255)->nullable();
            $table->string('father_work_address', 255)->nullable();
            $table->string('father_address', 255)->nullable();
            $table->string('father_city', 255)->nullable();
            $table->string('father_state', 255)->nullable();
            $table->string('father_pin_code', 255)->nullable();
            $table->string('father_alternate_mobile', 255)->nullable();
            $table->string('mother_aadhaar_last4', 4)->nullable();
            $table->string('mother_nationality', 255)->nullable();
            $table->string('mother_qualification', 255)->nullable();
            $table->string('mother_organization', 255)->nullable();
            $table->string('mother_work_address', 255)->nullable();
            $table->string('mother_address', 255)->nullable();
            $table->string('mother_city', 255)->nullable();
            $table->string('mother_state', 255)->nullable();
            $table->string('mother_pin_code', 255)->nullable();
            $table->string('mother_alternate_mobile', 255)->nullable();
            $table->string('guardian_aadhaar_last4', 4)->nullable();
            $table->string('guardian_nationality', 255)->nullable();
            $table->string('guardian_occupation', 255)->nullable();
            $table->string('guardian_email', 255)->nullable();
            $table->string('guardian_qualification', 255)->nullable();
            $table->string('guardian_address', 255)->nullable();
            $table->string('guardian_city', 255)->nullable();
            $table->string('guardian_state', 255)->nullable();
            $table->string('guardian_pin_code', 255)->nullable();
            $table->string('guardian_alternate_mobile', 255)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('student_admission_extra_details'); }
};
