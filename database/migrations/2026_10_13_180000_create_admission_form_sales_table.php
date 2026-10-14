<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('admission_form_sales', function(Blueprint $t) {
   $t->id();
   $t->foreignId('school_id')->constrained('schools')->restrictOnDelete();
   $t->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
   $t->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
   $t->unsignedBigInteger('admission_candidate_id')->nullable();
   $t->foreign('admission_candidate_id','adm_fs_candidate_fk')->references('id')->on('admission_candidates')->nullOnDelete();
   $t->string('form_no',40); $t->string('candidate_name',150); $t->string('father_name',150)->nullable();
   $t->string('mother_name',150)->nullable(); $t->string('mobile',20)->nullable();
   $t->date('date_of_birth')->nullable(); $t->string('gender',20)->nullable();
   $t->date('application_date'); $t->decimal('amount',12,2)->default(0);
   $t->string('receipt_no',60)->nullable(); $t->string('payment_mode',30)->default('Cash');
   $t->string('status',30)->default('form_issued'); $t->boolean('approved_for_registration')->default(false);
   $t->boolean('hostel_required')->default(false); $t->string('login_id',100)->nullable();
   $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
   $t->text('remarks')->nullable(); $t->timestamps();
   $t->unique(['school_id','form_no'],'adm_fs_school_form_unique');
   $t->index(['school_id','academic_year_id','status'],'adm_fs_school_year_status_idx');
  });
 }
 public function down(): void { Schema::dropIfExists('admission_form_sales'); }
};
