<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('erp_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('event', 50);
            $table->string('module', 100)->nullable();
            $table->string('description', 500)->nullable();
            $table->string('route_name', 180)->nullable();
            $table->string('http_method', 10)->nullable();
            $table->string('path', 500)->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['school_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['school_id', 'event', 'created_at']);
        });
    }
    public function down(): void { Schema::dropIfExists('erp_activity_logs'); }
};
