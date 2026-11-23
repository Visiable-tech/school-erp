<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique('users_username_unique');
        });
        // Existing email-based users retain their emails. New employee logins can have no email.
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }
    public function down(): void {
        // Refuse to restore NOT NULL email while username-only users exist.
        if (\Illuminate\Support\Facades\DB::table('users')->whereNull('email')->exists()) {
            throw new \RuntimeException('Assign emails to username-only users before rollback.');
        }
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
        });
    }
};
