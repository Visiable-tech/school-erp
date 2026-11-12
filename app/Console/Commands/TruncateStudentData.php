<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateStudentData extends Command
{
    protected $signature = 'students:truncate-data';

    protected $description = 'Truncate student-related ERP tables';

    public function handle()
    {
        if (!$this->confirm(
            'This will permanently delete student data from ALL schools and sessions. Continue?'
        )) {
            $this->warn('Operation cancelled.');
            return self::SUCCESS;
        }

        // Review against your actual database schema.
        // Child tables must be cleared before parent tables.
        $tables = [
            'student_documents',
            'student_attendances',
            'student_promotions',
            'student_enrollments',
            'student_fee_payments',
            'student_fee_collections',
            'student_fee_dues',
            'students',
        ];

        $existingTables = array_values(array_filter(
            $tables,
            fn ($table) => Schema::hasTable($table)
        ));

        $this->info('Tables selected: ' . implode(', ', $existingTables));

        if (!$this->confirm('Confirm permanent deletion of these tables?')) {
            return self::SUCCESS;
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            foreach ($existingTables as $table) {
                DB::table($table)->truncate();
                $this->info("Truncated: {$table}");
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->info('Selected student tables truncated.');

        return self::SUCCESS;
    }
}