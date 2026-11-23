<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('transport_route_stops',function(Blueprint $t){
   $t->id();$t->foreignId('transport_route_id')->constrained('transport_routes')->restrictOnDelete();
   $t->string('name');$t->unsignedInteger('sort_order')->default(0);$t->boolean('status')->default(true);$t->timestamps();
   $t->index(['transport_route_id','sort_order'],'trs_route_order_idx');
  });
  Schema::create('transport_stop_rates',function(Blueprint $t){
   $t->id();$t->foreignId('transport_route_stop_id')->constrained('transport_route_stops')->restrictOnDelete();
   $t->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
   $t->decimal('monthly_amount',12,2);$t->timestamps();
   $t->unique(['transport_route_stop_id','academic_year_id'],'tsr_stop_year_unique');
  });
  Schema::table('student_transport_assignments',function(Blueprint $t){
   $t->foreignId('transport_route_stop_id')->nullable()->after('transport_route_id')->constrained('transport_route_stops')->restrictOnDelete();
  });
 }
 public function down(): void {
  Schema::table('student_transport_assignments',function(Blueprint $t){$t->dropConstrainedForeignId('transport_route_stop_id');});
  Schema::dropIfExists('transport_stop_rates');Schema::dropIfExists('transport_route_stops');
 }
};
