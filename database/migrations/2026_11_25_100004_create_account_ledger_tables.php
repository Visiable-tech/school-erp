<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(){
 Schema::create('account_ledger_accounts',function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained('schools');$t->string('code',30);$t->string('name',150);$t->string('type',20);$t->unique(['school_id','code']);$t->timestamps();});
 Schema::create('account_journals',function(Blueprint $t){$t->id();$t->foreignId('school_id')->constrained('schools');$t->date('entry_date');$t->string('kind',20);$t->string('description',255);$t->string('status',20)->default('pending');$t->unsignedBigInteger('created_by');$t->unsignedBigInteger('reviewed_by')->nullable();$t->timestamp('reviewed_at')->nullable();$t->string('source',30)->nullable();$t->unsignedBigInteger('source_id')->nullable();$t->unique(['school_id','source','source_id'],'ledger_source_unique');$t->timestamps();});
 Schema::create('account_journal_lines',function(Blueprint $t){$t->id();$t->foreignId('journal_id')->constrained('account_journals');$t->foreignId('account_id')->constrained('account_ledger_accounts');$t->decimal('debit',14,2)->default(0);$t->decimal('credit',14,2)->default(0);});
 }
 public function down(){Schema::dropIfExists('account_journal_lines');Schema::dropIfExists('account_journals');Schema::dropIfExists('account_ledger_accounts');}
};
