<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('teacher_assessments', function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->index();$t->foreignId('teacher_user_id')->index();$t->foreignId('teacher_assignment_id')->index();
   $t->string('term',80);$t->string('title',160);$t->date('assessment_date');$t->decimal('max_marks',7,2);$t->timestamps();
  });
  Schema::create('teacher_assessment_marks', function(Blueprint $t){
   $t->id();$t->foreignId('assessment_id')->index();$t->foreignId('student_enrollment_id')->index();$t->decimal('marks',7,2)->nullable();$t->text('remarks')->nullable();$t->timestamps();
   $t->unique(['assessment_id','student_enrollment_id'],'tam_assessment_enrollment_uq');
  });
  Schema::create('teacher_term_remarks', function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->index();$t->foreignId('teacher_user_id');$t->foreignId('teacher_assignment_id')->index();$t->foreignId('student_enrollment_id')->index();$t->string('term',80);$t->text('remark');$t->timestamps();
   $t->unique(['teacher_assignment_id','student_enrollment_id','term'],'ttr_assignment_enrollment_term_uq');
  });
 }
 public function down(): void {Schema::dropIfExists('teacher_term_remarks');Schema::dropIfExists('teacher_assessment_marks');Schema::dropIfExists('teacher_assessments');}
};
