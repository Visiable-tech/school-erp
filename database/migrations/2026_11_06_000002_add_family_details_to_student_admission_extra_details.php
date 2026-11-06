<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('student_admission_extra_details')) return;
        Schema::table('student_admission_extra_details', function (Blueprint $table) {
            if (!Schema::hasColumn('student_admission_extra_details', 'father_aadhaar_last4')) $table->string('father_aadhaar_last4', 4)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_nationality')) $table->string('father_nationality', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_qualification')) $table->string('father_qualification', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_organization')) $table->string('father_organization', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_work_address')) $table->string('father_work_address', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_address')) $table->string('father_address', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_city')) $table->string('father_city', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_state')) $table->string('father_state', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_pin_code')) $table->string('father_pin_code', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'father_alternate_mobile')) $table->string('father_alternate_mobile', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_aadhaar_last4')) $table->string('mother_aadhaar_last4', 4)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_nationality')) $table->string('mother_nationality', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_qualification')) $table->string('mother_qualification', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_organization')) $table->string('mother_organization', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_work_address')) $table->string('mother_work_address', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_address')) $table->string('mother_address', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_city')) $table->string('mother_city', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_state')) $table->string('mother_state', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_pin_code')) $table->string('mother_pin_code', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'mother_alternate_mobile')) $table->string('mother_alternate_mobile', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_aadhaar_last4')) $table->string('guardian_aadhaar_last4', 4)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_nationality')) $table->string('guardian_nationality', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_occupation')) $table->string('guardian_occupation', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_email')) $table->string('guardian_email', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_qualification')) $table->string('guardian_qualification', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_address')) $table->string('guardian_address', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_city')) $table->string('guardian_city', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_state')) $table->string('guardian_state', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_pin_code')) $table->string('guardian_pin_code', 255)->nullable();
            if (!Schema::hasColumn('student_admission_extra_details', 'guardian_alternate_mobile')) $table->string('guardian_alternate_mobile', 255)->nullable();
        });
    }
    public function down(): void {
        if (!Schema::hasTable('student_admission_extra_details')) return;
        $columns = ['father_aadhaar_last4','father_nationality','father_qualification','father_organization','father_work_address','father_address','father_city','father_state','father_pin_code','father_alternate_mobile','mother_aadhaar_last4','mother_nationality','mother_qualification','mother_organization','mother_work_address','mother_address','mother_city','mother_state','mother_pin_code','mother_alternate_mobile','guardian_aadhaar_last4','guardian_nationality','guardian_occupation','guardian_email','guardian_qualification','guardian_address','guardian_city','guardian_state','guardian_pin_code','guardian_alternate_mobile'];
        $existing = array_values(array_filter($columns, fn($col) => Schema::hasColumn('student_admission_extra_details', $col)));
        if ($existing) Schema::table('student_admission_extra_details', fn(Blueprint $table) => $table->dropColumn($existing));
    }
};
