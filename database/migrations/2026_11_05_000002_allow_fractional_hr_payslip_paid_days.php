<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::table('hr_payslips',function(Blueprint $t){$t->decimal('paid_days',5,2)->change();});
 }
 public function down():void {
  // No automatic rollback: converting fractional days to integer loses precision.
 }
};
