<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {

            if (!Schema::hasColumn('fee_collections', 'payment_mode_id')) {
                $table->unsignedBigInteger('payment_mode_id')
                    ->nullable()
                    ->after('total_amount');

                $table->foreign('payment_mode_id', 'fc_payment_mode_fk')
                    ->references('id')
                    ->on('payment_modes')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('fee_collections', 'school_account_id')) {
                $table->unsignedBigInteger('school_account_id')
                    ->nullable()
                    ->after('payment_mode_id');

                $table->foreign('school_account_id', 'fc_school_account_fk')
                    ->references('id')
                    ->on('school_accounts')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_collections', function (Blueprint $table) {

            if (Schema::hasColumn('fee_collections', 'school_account_id')) {
                $table->dropForeign('fc_school_account_fk');
                $table->dropColumn('school_account_id');
            }

            if (Schema::hasColumn('fee_collections', 'payment_mode_id')) {
                $table->dropForeign('fc_payment_mode_fk');
                $table->dropColumn('payment_mode_id');
            }
        });
    }
};