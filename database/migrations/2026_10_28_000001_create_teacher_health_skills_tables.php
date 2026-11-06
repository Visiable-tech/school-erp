<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('teacher_student_health_records',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('student_enrollment_id')->index();$t->unsignedBigInteger('teacher_user_id');$t->date('recorded_on');$t->decimal('height_cm',5,2)->nullable();$t->decimal('weight_kg',5,2)->nullable();$t->string('blood_group',8)->nullable();$t->text('notes')->nullable();$t->timestamps();
   $t->unique(['student_enrollment_id','recorded_on'],'tshr_enrollment_day_uq');
  });
  Schema::create('teacher_skill_descriptors',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('teacher_assignment_id')->index();$t->string('term',80);$t->string('skill',160);$t->timestamps();
   $t->unique(['teacher_assignment_id','term','skill'],'tsd_assignment_term_skill_uq');
  });
  Schema::create('teacher_student_skill_ratings',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('teacher_skill_descriptor_id')->index();$t->unsignedBigInteger('student_enrollment_id')->index();$t->unsignedBigInteger('teacher_user_id');$t->string('rating',30)->nullable();$t->timestamps();
   $t->unique(['teacher_skill_descriptor_id','student_enrollment_id'],'tssr_skill_enrollment_uq');
  });
 }
 public function down(): void {Schema::dropIfExists('teacher_student_skill_ratings');Schema::dropIfExists('teacher_skill_descriptors');Schema::dropIfExists('teacher_student_health_records');}
};
