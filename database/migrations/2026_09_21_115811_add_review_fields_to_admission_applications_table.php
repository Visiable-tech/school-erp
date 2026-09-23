<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {

            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('application_status')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by');

            $table->foreignId('approved_by')
                ->nullable()
                ->after('reviewed_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')
                ->nullable()
                ->after('approved_by');

            $table->foreignId('rejected_by')
                ->nullable()
                ->after('approved_at')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')
                ->nullable()
                ->after('rejected_by');

            $table->text('review_remarks')
                ->nullable()
                ->after('rejected_at');
        });
    }

    public function down(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {

            $table->dropForeign(['reviewed_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['rejected_by']);

            $table->dropColumn([
                'reviewed_by',
                'reviewed_at',
                'approved_by',
                'approved_at',
                'rejected_by',
                'rejected_at',
                'review_remarks',
            ]);
        });
    }
};