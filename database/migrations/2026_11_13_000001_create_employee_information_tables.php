<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('erp_employees',function(Blueprint $t){$t->id();$t->foreignId('school_id')->index();$t->string('employee_code',40);$t->string('name');$t->string('email')->nullable();$t->string('phone',20)->nullable();$t->string('gender',15)->nullable();$t->date('dob')->nullable();$t->date('joining_date');$t->string('department')->nullable();$t->string('designation')->nullable();$t->string('employment_type')->nullable();$t->string('status',25)->default('active');$t->text('address')->nullable();$t->timestamps();$t->unique(['school_id','employee_code']);});
  Schema::create('erp_employee_events',function(Blueprint $t){$t->id();$t->foreignId('school_id')->index();$t->foreignId('employee_id')->constrained('erp_employees')->cascadeOnDelete();$t->string('type',40);$t->date('event_date');$t->date('end_date')->nullable();$t->string('title')->nullable();$t->text('remarks')->nullable();$t->timestamps();$t->index(['school_id','type']);});
  Schema::create('erp_employee_certificates',function(Blueprint $t){$t->id();$t->foreignId('school_id')->index();$t->foreignId('employee_id')->constrained('erp_employees')->cascadeOnDelete();$t->string('title');$t->string('file_path');$t->date('expiry_date')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('erp_employee_certificates');Schema::dropIfExists('erp_employee_events');Schema::dropIfExists('erp_employees');}
};
