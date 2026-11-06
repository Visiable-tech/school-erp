<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('teacher_messages', function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->index();
   $t->foreignId('sender_user_id')->index();$t->foreignId('recipient_user_id')->index();
   $t->string('subject',180);$t->text('body');$t->timestamp('read_at')->nullable();$t->timestamps();
   $t->index(['school_id','recipient_user_id','read_at']);
  });
  Schema::create('teacher_notices',function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->index();$t->foreignId('created_by')->index();
   $t->string('type',20);$t->string('title',180);$t->text('body');
   $t->foreignId('academic_year_id')->nullable()->index();$t->foreignId('school_class_id')->nullable()->index();
   $t->foreignId('section_id')->nullable()->index();$t->date('published_on');$t->timestamps();
   $t->index(['school_id','type','published_on']);
  });
  Schema::create('teacher_student_remarks',function(Blueprint $t){
   $t->id();$t->foreignId('school_id')->index();$t->foreignId('teacher_user_id')->index();
   $t->foreignId('student_enrollment_id')->index();$t->string('category',30);$t->text('remark');$t->date('remark_date');$t->timestamps();
  });
 }
 public function down():void{
  Schema::dropIfExists('teacher_student_remarks');Schema::dropIfExists('teacher_notices');Schema::dropIfExists('teacher_messages');
 }
};
