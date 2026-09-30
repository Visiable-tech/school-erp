<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_cheque_dd_details', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('school_id');

            /*
            |--------------------------------------------------------------------------
            | Payment / Receipt
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('fee_collection_id');

            /*
            |--------------------------------------------------------------------------
            | Instrument Type
            |--------------------------------------------------------------------------
            */

            $table->enum('instrument_type', [
                'cheque',
                'dd'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Cheque / DD Information
            |--------------------------------------------------------------------------
            */

            $table->string('instrument_no', 100);

            $table->date('instrument_date');

            /*
            |--------------------------------------------------------------------------
            | Bank
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('bank_master_id')
                ->nullable();

            $table->string('bank_name', 150)
                ->nullable();

            $table->string('branch_name', 150)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Amount
            |--------------------------------------------------------------------------
            */

            $table->decimal('amount', 12, 2);

            /*
            |--------------------------------------------------------------------------
            | Deposit Information
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('school_account_id')
                ->nullable();

            $table->date('deposit_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Clearing
            |--------------------------------------------------------------------------
            */

            $table->enum('clearance_status', [
                'pending',
                'deposited',
                'cleared',
                'bounced',
                'cancelled'
            ])->default('pending');

            $table->date('clearance_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Bounce
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('cheque_bounce_reason_id')
                ->nullable();

            $table->date('bounce_date')
                ->nullable();

            $table->text('bounce_remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Remarks
            |--------------------------------------------------------------------------
            */

            $table->text('remarks')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->unsignedBigInteger('updated_by')
                ->nullable();

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign(
                'school_id',
                'fcdd_school_fk'
            )
            ->references('id')
            ->on('schools')
            ->restrictOnDelete();


            $table->foreign(
                'fee_collection_id',
                'fcdd_collection_fk'
            )
            ->references('id')
            ->on('fee_collections')
            ->restrictOnDelete();


            $table->foreign(
                'bank_master_id',
                'fcdd_bank_fk'
            )
            ->references('id')
            ->on('bank_masters')
            ->nullOnDelete();


            $table->foreign(
                'school_account_id',
                'fcdd_account_fk'
            )
            ->references('id')
            ->on('school_accounts')
            ->nullOnDelete();


            $table->foreign(
                'cheque_bounce_reason_id',
                'fcdd_bounce_reason_fk'
            )
            ->references('id')
            ->on('cheque_bounce_reasons')
            ->nullOnDelete();


            $table->foreign(
                'created_by',
                'fcdd_created_by_fk'
            )
            ->references('id')
            ->on('users')
            ->nullOnDelete();


            $table->foreign(
                'updated_by',
                'fcdd_updated_by_fk'
            )
            ->references('id')
            ->on('users')
            ->nullOnDelete();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->unique(
                'fee_collection_id',
                'fcdd_collection_unique'
            );


            $table->index(
                [
                    'school_id',
                    'clearance_status'
                ],
                'fcdd_status_idx'
            );


            $table->index(
                [
                    'school_id',
                    'instrument_no'
                ],
                'fcdd_instrument_idx'
            );


            $table->index(
                'instrument_date',
                'fcdd_date_idx'
            );
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'fee_cheque_dd_details'
        );
    }
};