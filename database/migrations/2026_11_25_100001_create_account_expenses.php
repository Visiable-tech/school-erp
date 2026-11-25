<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('account_expense_categories', function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained();$t->string('name',150);$t->boolean('active')->default(true);$t->unique(['school_id','name']);$t->timestamps();});
  Schema::create('account_expenses', function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained();$t->foreignId('academic_year_id')->constrained();$t->foreignId('category_id')->constrained('account_expense_categories');$t->date('expense_date');$t->string('payee');$t->decimal('amount',12,2);$t->text('description');$t->string('invoice_reference')->nullable();$t->string('status',20)->default('pending');$t->foreignId('created_by')->constrained('users');$t->foreignId('reviewed_by')->nullable()->constrained('users');$t->timestamp('reviewed_at')->nullable();$t->text('review_note')->nullable();$t->timestamps();$t->index(['school_id','academic_year_id','status']);});
  Schema::create('account_expense_payments', function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained();$t->foreignId('expense_id')->unique()->constrained('account_expenses');$t->date('payment_date');$t->decimal('amount',12,2);$t->string('payment_mode',30);$t->string('reference')->nullable();$t->foreignId('paid_by')->constrained('users');$t->timestamps();$t->index(['school_id','payment_date']);});
 }
 public function down(): void {Schema::dropIfExists('account_expense_payments');Schema::dropIfExists('account_expenses');Schema::dropIfExists('account_expense_categories');}
};
