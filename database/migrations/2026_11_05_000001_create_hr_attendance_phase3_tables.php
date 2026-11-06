<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('hr_employee_shift_assignments',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->unsignedBigInteger('shift_id');$t->date('effective_from');$t->date('effective_to')->nullable();
   $t->timestamps();$t->index(['school_id','user_id','effective_from'],'hr_shift_user_effective');
  });
  Schema::create('hr_daily_attendance',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->date('attendance_date');$t->unsignedBigInteger('attendance_status_id')->index();
   $t->time('check_in')->nullable();$t->time('check_out')->nullable();
   $t->unsignedInteger('overtime_minutes')->default(0);
   $t->text('note')->nullable();$t->unsignedBigInteger('marked_by');$t->timestamps();
   $t->unique(['school_id','user_id','attendance_date'],'hr_daily_unique');
  });
  Schema::create('hr_employee_paid_day_overrides',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->char('pay_month',7);$t->decimal('paid_days',5,2);
   $t->unsignedBigInteger('set_by');$t->text('calculation_note')->nullable();$t->timestamps();
   $t->unique(['school_id','user_id','pay_month'],'hr_paid_days_unique');
  });
 }
 public function down():void {
  Schema::dropIfExists('hr_employee_paid_day_overrides');
  Schema::dropIfExists('hr_daily_attendance');
  Schema::dropIfExists('hr_employee_shift_assignments');
 }
};
