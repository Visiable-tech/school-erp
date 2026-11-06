<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('teacher_staff_attendances',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->date('attendance_date');$t->string('status',15);$t->string('note',500)->nullable();
   $t->unsignedBigInteger('marked_by')->nullable();$t->timestamps();
   $t->unique(['school_id','user_id','attendance_date'],'tsa_school_user_day_uq');
  });
  Schema::create('teacher_staff_leave_requests',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->date('start_date');$t->date('end_date');$t->string('leave_type',30);
   $t->text('reason');$t->string('status',15)->default('pending');
   $t->unsignedBigInteger('reviewed_by')->nullable();$t->timestamp('reviewed_at')->nullable();
   $t->string('review_note',500)->nullable();$t->timestamps();
  });
  Schema::create('teacher_staff_attendance_changes',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->date('attendance_date');$t->string('requested_status',15);$t->text('reason');
   $t->string('status',15)->default('pending');$t->unsignedBigInteger('reviewed_by')->nullable();
   $t->timestamp('reviewed_at')->nullable();$t->string('review_note',500)->nullable();$t->timestamps();
  });
 }
 public function down():void {
  Schema::dropIfExists('teacher_staff_attendance_changes');
  Schema::dropIfExists('teacher_staff_leave_requests');
  Schema::dropIfExists('teacher_staff_attendances');
 }
};
