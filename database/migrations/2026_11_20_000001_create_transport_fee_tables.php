<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('transport_routes', function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained()->restrictOnDelete();$t->string('code',30);$t->string('name');$t->boolean('status')->default(true);$t->timestamps();$t->unique(['school_id','code']);});
  Schema::create('transport_route_rates',function(Blueprint $t){$t->id();$t->foreignId('transport_route_id')->constrained()->restrictOnDelete();$t->foreignId('academic_year_id')->constrained()->restrictOnDelete();$t->decimal('monthly_amount',12,2);$t->timestamps();$t->unique(['transport_route_id','academic_year_id'],'transport_route_year_unique');});
  Schema::create('student_transport_assignments',function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained()->restrictOnDelete();$t->foreignId('student_id')->constrained()->restrictOnDelete();$t->foreignId('student_enrollment_id')->constrained()->restrictOnDelete();$t->foreignId('academic_year_id')->constrained()->restrictOnDelete();$t->foreignId('transport_route_id')->constrained()->restrictOnDelete();$t->date('starts_on');$t->date('ends_on')->nullable();$t->decimal('monthly_amount',12,2);$t->timestamps();$t->index(['student_enrollment_id','starts_on'],'sta_enrollment_start_idx');});
  Schema::table('student_fee_dues',function(Blueprint $t){$t->foreignId('student_transport_assignment_id')->nullable()->after('student_optional_fee_assignment_id')->constrained('student_transport_assignments')->restrictOnDelete();$t->unique(['student_transport_assignment_id','period_start'],'transport_due_month_unique');});
 }
 public function down(): void {Schema::table('student_fee_dues',function(Blueprint $t){$t->dropUnique('transport_due_month_unique');$t->dropConstrainedForeignId('student_transport_assignment_id');});Schema::dropIfExists('student_transport_assignments');Schema::dropIfExists('transport_route_rates');Schema::dropIfExists('transport_routes');}
};
