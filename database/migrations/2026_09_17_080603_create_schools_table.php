<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {

            $table->id();

            $table->string('school_code', 50)->unique();
            $table->string('school_name');

            $table->string('short_name')->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('alternate_phone', 20)->nullable();

            $table->text('address')->nullable();

            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('pin_code', 10)->nullable();

            $table->string('board')->nullable();
            $table->string('affiliation_no')->nullable();

            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            $table->string('website')->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
