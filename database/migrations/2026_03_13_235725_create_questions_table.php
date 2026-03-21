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
       Schema::create('questions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('subject_id')->constrained()->onDelete('cascade');
    $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
    $table->enum('exam_type', ['JAMB', 'WAEC', 'NECO', 'Post-UTME'])->default('JAMB');
    $table->year('year');
    $table->text('question_text');
    $table->text('option_a');
    $table->text('option_b');
    $table->text('option_c');
    $table->text('option_d');
    $table->char('correct_answer', 1)->nullable(); // A, B, C or D
    $table->longText('explanation')->nullable();   // scraped directly, no AI needed
    $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
    $table->string('image_url')->nullable();       // for questions with diagrams
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
