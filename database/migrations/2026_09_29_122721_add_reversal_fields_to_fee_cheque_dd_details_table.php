<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_cheque_dd_details', function (Blueprint $table) {

            $table->timestamp('payment_reversed_at')
                ->nullable()
                ->after('bounce_remarks');

            $table->foreignId('payment_reversed_by')
                ->nullable()
                ->after('payment_reversed_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->decimal('bounce_charge_amount', 12, 2)
                ->default(0)
                ->after('payment_reversed_by');

            $table->boolean('bounce_charge_applied')
                ->default(false)
                ->after('bounce_charge_amount');
        });
    }

    public function down(): void
    {
        Schema::table('fee_cheque_dd_details', function (Blueprint $table) {

            $table->dropForeign([
                'payment_reversed_by'
            ]);

            $table->dropColumn([
                'payment_reversed_at',
                'payment_reversed_by',
                'bounce_charge_amount',
                'bounce_charge_applied',
            ]);
        });
    }
};