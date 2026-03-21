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
    Schema::create('discount_codes', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->integer('percent')->default(10);
        $table->integer('max_uses')->nullable();
        $table->integer('used_count')->default(0);
        $table->date('expires_at')->nullable();
        $table->boolean('is_active')->default(true);
        $table->string('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
