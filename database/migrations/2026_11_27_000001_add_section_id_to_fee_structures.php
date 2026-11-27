<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('school_class_id')->constrained('sections')->restrictOnDelete();
        });
    }
    public function down(): void {
        Schema::table('fee_structures', function (Blueprint $table) { $table->dropConstrainedForeignId('section_id'); });
    }
};
