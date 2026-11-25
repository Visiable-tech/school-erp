<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
 Schema::create('account_vendors',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->string('name');$t->string('category',100);$t->string('contact')->nullable();$t->string('phone',30)->nullable();$t->string('email')->nullable();$t->string('gstin',15)->nullable();$t->text('address')->nullable();$t->boolean('active')->default(true);$t->timestamps();});
 Schema::create('account_vendor_invoices',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->foreignId('vendor_id')->constrained('account_vendors');$t->unsignedBigInteger('academic_year_id');$t->string('invoice_number',100);$t->date('invoice_date');$t->date('due_date');$t->decimal('amount',12,2);$t->text('description');$t->string('status',20)->default('pending');$t->unsignedBigInteger('created_by');$t->unsignedBigInteger('reviewed_by')->nullable();$t->timestamp('reviewed_at')->nullable();$t->text('review_note')->nullable();$t->timestamps();$t->unique(['school_id','vendor_id','invoice_number'],'vendor_bill_unique');});
 Schema::create('account_vendor_payments',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->foreignId('invoice_id')->constrained('account_vendor_invoices');$t->date('payment_date');$t->decimal('amount',12,2);$t->string('payment_mode',30);$t->string('reference')->nullable();$t->uuid('request_key');$t->unsignedBigInteger('paid_by');$t->timestamps();$t->unique(['school_id','request_key']);});
 }
 public function down(): void {Schema::dropIfExists('account_vendor_payments');Schema::dropIfExists('account_vendor_invoices');Schema::dropIfExists('account_vendors');}
};
