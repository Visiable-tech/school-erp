<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('hr_salary_profiles',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id');
   foreach(['basic','hra','allowance','other_earning','pf','professional_tax','tds','other_deduction'] as $col)$t->decimal($col,12,2)->default(0);
   $t->boolean('is_active')->default(true);$t->timestamps();$t->unique(['school_id','user_id']);
  });
  Schema::create('hr_salary_advances',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->decimal('amount',12,2);$t->decimal('monthly_recovery',12,2);
   $t->date('approved_on')->nullable();$t->string('status',20)->default('pending');
   $t->string('reason',500)->nullable();$t->unsignedBigInteger('reviewed_by')->nullable();
   $t->timestamps();
  });
  Schema::create('hr_payroll_runs',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->char('pay_month',7);
   $t->unsignedSmallInteger('working_days');$t->string('status',20)->default('draft');
   $t->unsignedBigInteger('created_by');$t->unsignedBigInteger('finalized_by')->nullable();
   $t->timestamp('finalized_at')->nullable();$t->timestamps();$t->unique(['school_id','pay_month']);
  });
  Schema::create('hr_payroll_adjustments',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('user_id')->index();
   $t->char('pay_month',7);$t->string('type',15);$t->string('label',150);
   $t->decimal('amount',12,2);$t->timestamps();$t->index(['school_id','pay_month']);
  });
  Schema::create('hr_payslips',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('payroll_run_id')->index();
   $t->unsignedBigInteger('user_id')->index();$t->char('pay_month',7);
   $t->unsignedSmallInteger('working_days');$t->unsignedSmallInteger('paid_days');
   $t->decimal('gross_salary',12,2);$t->decimal('prorated_earnings',12,2);
   $t->decimal('adjustment_earnings',12,2)->default(0);$t->decimal('base_deductions',12,2)->default(0);
   $t->decimal('adjustment_deductions',12,2)->default(0);$t->decimal('advance_recovery',12,2)->default(0);
   $t->decimal('net_salary',12,2);$t->json('breakdown');$t->timestamps();
   $t->unique(['school_id','user_id','pay_month'],'hr_payslip_school_user_month');
  });
  Schema::create('hr_advance_recoveries',function(Blueprint $t){
   $t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('salary_advance_id')->index();
   $t->unsignedBigInteger('payslip_id')->index();$t->decimal('amount',12,2);$t->timestamps();
   $t->unique(['salary_advance_id','payslip_id']);
  });
 }
 public function down():void {
  foreach(['hr_advance_recoveries','hr_payslips','hr_payroll_adjustments','hr_payroll_runs','hr_salary_advances','hr_salary_profiles'] as $t)Schema::dropIfExists($t);
 }
};
