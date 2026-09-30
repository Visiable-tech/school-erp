<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_certificates', function (Blueprint $table) {

            $table->boolean('is_manual')
                ->default(false)
                ->after('school_id');

            $table->string('manual_admission_no', 100)
                ->nullable()
                ->after('is_manual');

            $table->string('manual_student_name', 150)
                ->nullable()
                ->after('manual_admission_no');

            $table->string('manual_father_name', 150)
                ->nullable()
                ->after('manual_student_name');

            $table->string('manual_mother_name', 150)
                ->nullable()
                ->after('manual_father_name');

            $table->date('manual_date_of_birth')
                ->nullable()
                ->after('manual_mother_name');

            $table->string('manual_class', 100)
                ->nullable()
                ->after('manual_date_of_birth');

            $table->string('manual_section', 100)
                ->nullable()
                ->after('manual_class');

            $table->string('manual_academic_year', 100)
                ->nullable()
                ->after('manual_section');
        });
    }

    public function down(): void
    {
        Schema::table('transfer_certificates', function (Blueprint $table) {

            $table->dropColumn([
                'is_manual',
                'manual_admission_no',
                'manual_student_name',
                'manual_father_name',
                'manual_mother_name',
                'manual_date_of_birth',
                'manual_class',
                'manual_section',
                'manual_academic_year',
            ]);
        });
    }
};