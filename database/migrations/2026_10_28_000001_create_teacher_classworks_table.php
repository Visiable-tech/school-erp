<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('teacher_classworks',function(Blueprint $t){
   $t->id(); $t->foreignId('school_id')->constrained('schools');
   $t->foreignId('teacher_assignment_id')->constrained('teacher_assignments');
   $t->foreignId('teacher_user_id')->constrained('users');
   $t->date('work_date'); $t->string('title',200); $t->enum('type',['classwork','homework','assignment']);
   $t->text('description'); $t->date('due_date')->nullable();
   $t->enum('approval_status',['pending','approved','rejected'])->default('pending');
   $t->text('review_note')->nullable(); $t->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
   $t->timestamp('reviewed_at')->nullable(); $t->timestamps();
   $t->index(['school_id','work_date','approval_status'],'tcw_school_date_status');
  });
 }
 public function down(): void { Schema::dropIfExists('teacher_classworks'); }
};
