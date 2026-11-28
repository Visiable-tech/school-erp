<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('transport_operation_records',function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained()->restrictOnDelete();$t->string('type',30);$t->string('title');$t->date('record_date')->nullable();$t->date('expiry_date')->nullable();$t->decimal('amount',12,2)->nullable();$t->string('status',30);$t->json('details');$t->timestamps();$t->index(['school_id','type','record_date'],'transport_ops_lookup');}); }
 public function down(): void {Schema::dropIfExists('transport_operation_records');}
};
