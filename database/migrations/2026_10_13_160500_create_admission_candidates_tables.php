
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_candidates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')
                ->constrained('schools')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->nullable()
                ->constrained('academic_years')
                ->nullOnDelete();

            $table->foreignId('school_class_id')
                ->nullable()
                ->constrained('school_classes')
                ->nullOnDelete();

            $table->string('candidate_name', 150);
            $table->string('father_name', 150)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('photo_path')->nullable();

            $table->string('status', 30)->default('new');

            $table->unsignedBigInteger('admission_enquiry_id')->nullable();
            $table->unsignedBigInteger('admission_application_id')->nullable();

            $table->timestamps();

            $table->index(
                ['school_id', 'status'],
                'adm_cand_school_status_idx'
            );

            $table->index(
                ['school_id', 'candidate_name'],
                'adm_cand_school_name_idx'
            );
        });

        Schema::create('admission_candidate_status_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('admission_candidate_id');

            $table->foreign(
                'admission_candidate_id',
                'adm_cand_hist_candidate_fk'
            )
                ->references('id')
                ->on('admission_candidates')
                ->cascadeOnDelete();

            $table->string('status', 30);

            $table->string('enquiry_level', 100)->nullable();
            $table->string('registration_level', 100)->nullable();

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['admission_candidate_id', 'created_at'],
                'adm_cand_hist_date_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_candidate_status_histories');
        Schema::dropIfExists('admission_candidates');
    }
};
