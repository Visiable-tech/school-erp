<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('teacher_period_attendance',function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->constrained('schools');$t->foreignId('academic_year_id')->constrained('academic_years');
   $t->foreignId('teacher_assignment_id')->constrained('teacher_assignments');$t->foreignId('student_enrollment_id')->constrained('student_enrollments');
   $t->date('attendance_date');$t->unsignedTinyInteger('period_number');$t->enum('attendance_status',['present','absent','late','leave']);
   $t->foreignId('marked_by')->constrained('users');$t->timestamps();
   $t->unique(['teacher_assignment_id','student_enrollment_id','attendance_date','period_number'],'tpa_assignment_student_date_period_uq');
  });
  Schema::create('teacher_student_leaves',function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->constrained('schools');$t->foreignId('academic_year_id')->constrained('academic_years');
   $t->foreignId('student_enrollment_id')->constrained('student_enrollments');$t->date('start_date');$t->date('end_date');
   $t->text('reason');$t->enum('status',['pending','approved','rejected'])->default('pending');$t->foreignId('requested_by')->constrained('users');
   $t->foreignId('reviewed_by')->nullable()->constrained('users');$t->timestamp('reviewed_at')->nullable();$t->text('review_note')->nullable();$t->timestamps();
   $t->index(['school_id','academic_year_id','status'],'tsl_school_year_status_idx');
  });
 }
 public function down():void {Schema::dropIfExists('teacher_student_leaves');Schema::dropIfExists('teacher_period_attendance');}
};
