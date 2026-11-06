<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('teacher_performance_reviews',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();
   $t->unsignedBigInteger('teacher_user_id')->index();
   $t->unsignedBigInteger('reviewer_user_id')->nullable()->index();
   $t->string('review_type',15); // self / team
   $t->string('period',7); // YYYY-MM
   $t->unsignedTinyInteger('teaching_rating');
   $t->unsignedTinyInteger('classroom_rating');
   $t->unsignedTinyInteger('communication_rating');
   $t->unsignedTinyInteger('professionalism_rating');
   $t->text('strengths');$t->text('improvement_areas');$t->text('goals')->nullable();
   $t->string('status',15)->default('draft');
   $t->text('review_note')->nullable();$t->unsignedBigInteger('reviewed_by')->nullable();
   $t->timestamp('reviewed_at')->nullable();$t->timestamps();
   $t->unique(['school_id','teacher_user_id','review_type','period'],'tpr_one_per_period');
  });
 }
 public function down():void {Schema::dropIfExists('teacher_performance_reviews');}
};
