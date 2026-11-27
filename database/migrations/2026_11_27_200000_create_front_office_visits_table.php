<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('front_office_visits', function(Blueprint $t){
 $t->id(); $t->foreignId('school_id')->constrained('schools'); $t->date('visit_date'); $t->time('in_time'); $t->time('out_time')->nullable(); $t->string('visitor_name'); $t->string('mobile',30)->nullable(); $t->string('visitor_type',30); $t->unsignedSmallInteger('team_size'); $t->string('meet_person'); $t->string('student_name')->nullable(); $t->text('purpose'); $t->text('remarks')->nullable(); $t->foreignId('created_by')->constrained('users'); $t->timestamps(); $t->index(['school_id','visit_date']); }); }
 public function down(): void { Schema::dropIfExists('front_office_visits'); }
};
