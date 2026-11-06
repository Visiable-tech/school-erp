<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('teacher_leave_entitlements',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->unsignedSmallInteger('year');$t->string('leave_type',30);
   $t->decimal('entitled_days',7,2)->default(0);$t->timestamps();
   $t->unique(['school_id','user_id','year','leave_type'],'tle_unique');
  });
  Schema::create('teacher_investment_declarations',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->string('financial_year',9);$t->string('category',100);
   $t->decimal('declared_amount',12,2);$t->string('description',500)->nullable();
   $t->string('status',15)->default('pending');$t->unsignedBigInteger('reviewed_by')->nullable();
   $t->timestamp('reviewed_at')->nullable();$t->string('review_note',500)->nullable();$t->timestamps();
  });
  Schema::create('teacher_resignation_requests',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->date('requested_last_day');$t->text('reason');
   $t->string('status',15)->default('pending');$t->unsignedBigInteger('reviewed_by')->nullable();
   $t->timestamp('reviewed_at')->nullable();$t->string('review_note',500)->nullable();$t->timestamps();
  });
 }
 public function down():void {
  Schema::dropIfExists('teacher_resignation_requests');
  Schema::dropIfExists('teacher_investment_declarations');
  Schema::dropIfExists('teacher_leave_entitlements');
 }
};
