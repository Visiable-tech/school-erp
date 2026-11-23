<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('student_app_accounts', function(Blueprint $t){
 $t->id(); $t->foreignId('school_id')->constrained('schools'); $t->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
 $t->string('login_id',50)->unique(); $t->string('password'); $t->boolean('is_active')->default(true); $t->boolean('must_change_password')->default(true); $t->timestamp('last_login_at')->nullable(); $t->timestamps();
 }); }
 public function down():void { Schema::dropIfExists('student_app_accounts'); }
};
