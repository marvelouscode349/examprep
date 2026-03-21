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
    Schema::create('user_subject_performance', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('subject_id')->constrained()->onDelete('cascade');
        $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
        $table->integer('total_answered')->default(0);
        $table->integer('total_correct')->default(0);
        $table->integer('accuracy')->default(0); // percentage 0-100
        $table->timestamp('last_practiced_at')->nullable();
        $table->unique(['user_id', 'subject_id', 'topic_id']); // one row per user per subject per topic
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subject_performance');
    }
};
