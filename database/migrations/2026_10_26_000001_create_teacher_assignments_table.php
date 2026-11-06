<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('teacher_assignments', function(Blueprint $t) {
   $t->id();
   $t->foreignId('school_id')->constrained('schools')->restrictOnDelete();
   $t->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
   $t->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
   $t->foreignId('school_class_id')->constrained('school_classes')->restrictOnDelete();
   $t->foreignId('section_id')->constrained('sections')->restrictOnDelete();
   $t->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
   $t->boolean('is_class_teacher')->default(false);
   $t->boolean('status')->default(true);$t->timestamps();
   $t->unique(['teacher_user_id','academic_year_id','section_id','subject_id'],'teach_assign_unique');
   $t->index(['school_id','teacher_user_id','status'],'teach_assign_scope_idx');
  });
 }
 public function down(): void {Schema::dropIfExists('teacher_assignments');}
};
