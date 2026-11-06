<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('hr_attendance_statuses',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();
   $t->string('name',100);$t->string('code',15);
   $t->string('count_as',30)->default('present');
   $t->decimal('extra_deduction_days',5,2)->default(0);
   $t->string('bg_colour',20)->nullable();$t->string('font_colour',20)->nullable();
   $t->boolean('is_active')->default(true);$t->boolean('document_required')->default(false);
   $t->unsignedInteger('monthly_request_limit')->nullable();
   $t->unsignedInteger('quarterly_request_limit')->nullable();
   $t->unsignedInteger('yearly_request_limit')->nullable();
   $t->timestamps();$t->unique(['school_id','code']);
  });
  Schema::create('hr_master_options',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();
   $t->string('category',60)->index();$t->string('name',150);
   $t->string('code',40)->nullable();$t->text('description')->nullable();
   $t->boolean('is_active')->default(true);$t->timestamps();
   $t->unique(['school_id','category','name'],'hr_option_school_category_name');
  });
  Schema::create('hr_shift_definitions',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();
   $t->string('name',100);$t->string('code',20);
   $t->time('start_time');$t->time('end_time');
   $t->unsignedInteger('grace_minutes')->default(0);
   $t->boolean('is_active')->default(true);$t->timestamps();
   $t->unique(['school_id','code']);
  });
 }
 public function down():void {
  Schema::dropIfExists('hr_shift_definitions');
  Schema::dropIfExists('hr_master_options');
  Schema::dropIfExists('hr_attendance_statuses');
 }
};
