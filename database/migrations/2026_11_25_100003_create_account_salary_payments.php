<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){Schema::create('account_salary_payments',function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained('schools');$t->foreignId('payslip_id')->constrained('hr_payslips');$t->date('payment_date');$t->decimal('amount',12,2);$t->string('payment_mode',30);$t->string('reference')->nullable();$t->uuid('request_key')->unique();$t->foreignId('paid_by')->constrained('users');$t->timestamps();$t->index(['school_id','payment_date']);});}
 public function down(){Schema::dropIfExists('account_salary_payments');}
};
