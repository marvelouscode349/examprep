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
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->nullable();
        $table->string('password');
        $table->enum('target_exam', ['JAMB', 'WAEC', 'NECO', 'Post-UTME', 'JAMB+WAEC'])->nullable();
        $table->enum('stream', ['science', 'arts', 'commercial', 'general'])->default('science');
        $table->string('exam_year', 4)->default('2026');
        $table->string('state')->nullable();
        $table->enum('subscription_status', ['free', 'active', 'expired'])->default('free');
        $table->timestamp('subscription_expires_at')->nullable();
        $table->string('referral_code')->nullable(); // marketer code used at signup
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
