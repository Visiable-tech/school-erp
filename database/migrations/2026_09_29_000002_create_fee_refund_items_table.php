<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('fee_refund_items', function(Blueprint $table){
  $table->id(); $table->unsignedBigInteger('school_id'); $table->unsignedBigInteger('fee_refund_id'); $table->unsignedBigInteger('fee_collection_item_id'); $table->unsignedBigInteger('student_fee_due_id'); $table->string('fee_head_name',150); $table->string('installment_name',150)->nullable(); $table->date('due_date')->nullable(); $table->decimal('original_paid_amount',12,2); $table->decimal('refund_amount',12,2); $table->timestamps();
  $table->foreign('school_id','fri_school_fk')->references('id')->on('schools')->restrictOnDelete(); $table->foreign('fee_refund_id','fri_refund_fk')->references('id')->on('fee_refunds')->cascadeOnDelete(); $table->foreign('fee_collection_item_id','fri_coll_item_fk')->references('id')->on('fee_collection_items')->restrictOnDelete(); $table->foreign('student_fee_due_id','fri_due_fk')->references('id')->on('student_fee_dues')->restrictOnDelete(); $table->index(['fee_collection_item_id','fee_refund_id'],'fri_collection_idx');
 }); }
 public function down(): void { Schema::dropIfExists('fee_refund_items'); }
};
