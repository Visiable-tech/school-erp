
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_collections', 'transaction_no')) {
                $table->string('transaction_no', 100)
                    ->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {
            if (Schema::hasColumn('fee_collections', 'transaction_no')) {
                $table->dropColumn('transaction_no');
            }
        });
    }
};
