<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('teacher_library_books',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->string('title',200);
   $t->string('author',150)->nullable();$t->string('isbn',30)->nullable();
   $t->string('category',100)->nullable();$t->string('shelf',100)->nullable();
   $t->unsignedInteger('total_copies')->default(1);$t->unsignedInteger('available_copies')->default(1);
   $t->boolean('status')->default(true);$t->timestamps();
   $t->index(['school_id','title']);
  });
  Schema::create('teacher_library_loans',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();
   $t->unsignedBigInteger('book_id')->index();$t->unsignedBigInteger('borrower_user_id')->index();
   $t->unsignedBigInteger('issued_by')->index();$t->unsignedBigInteger('returned_by')->nullable();
   $t->date('issued_on');$t->date('due_on');$t->date('returned_on')->nullable();
   $t->string('remarks',500)->nullable();$t->timestamps();
   $t->index(['school_id','borrower_user_id','returned_on'],'tll_borrower_idx');
  });
 }
 public function down(): void {
  Schema::dropIfExists('teacher_library_loans');
  Schema::dropIfExists('teacher_library_books');
 }
};
