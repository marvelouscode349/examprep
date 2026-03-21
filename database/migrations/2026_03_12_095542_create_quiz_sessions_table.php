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
    Schema::create('quiz_sessions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('subject_id')->constrained()->onDelete('cascade');
        $table->enum('mode', ['practice', 'mock', 'weak_areas', 'topic']);
        $table->enum('exam_type', ['JAMB', 'WAEC', 'NECO', 'Post-UTME'])->default('JAMB');
        $table->integer('total_questions')->default(0);
        $table->integer('correct')->default(0);
        $table->integer('wrong')->default(0);
        $table->integer('skipped')->default(0);
        $table->integer('score_percentage')->default(0);
        $table->integer('time_taken')->default(0); // seconds
        $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
        $table->timestamp('started_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_sessions');
    }
};
