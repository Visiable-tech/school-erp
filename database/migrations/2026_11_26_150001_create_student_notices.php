<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
 Schema::create('student_notices',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('academic_year_id');$t->unsignedBigInteger('created_by');$t->string('title');$t->longText('body');$t->string('recipient_type',20);$t->unsignedBigInteger('school_class_id')->nullable();$t->unsignedBigInteger('section_id')->nullable();$t->unsignedBigInteger('student_id')->nullable();$t->string('priority',20);$t->string('status',20);$t->dateTime('publish_at');$t->dateTime('expires_at')->nullable();$t->timestamps();});
 Schema::create('student_notice_recipients',function(Blueprint $t){$t->id();$t->foreignId('notice_id')->constrained('student_notices')->cascadeOnDelete();$t->unsignedBigInteger('student_id');$t->timestamp('read_at')->nullable();$t->unique(['notice_id','student_id']);});
 Schema::create('student_notice_attachments',function(Blueprint $t){$t->id();$t->foreignId('notice_id')->constrained('student_notices')->cascadeOnDelete();$t->string('path');$t->string('original_name');$t->string('mime');$t->unsignedBigInteger('size');});
 }
 public function down(): void {Schema::dropIfExists('student_notice_attachments');Schema::dropIfExists('student_notice_recipients');Schema::dropIfExists('student_notices');}
};
