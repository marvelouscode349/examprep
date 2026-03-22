<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportAlocQuestions extends Command
{
    protected $signature = 'import:aloc
                            {subject : e.g. chemistry, mathematics, english, englishlit, history, insurance}
                            {--type=utme : utme, wassce or post-utme}
                            {--delay=1 : seconds between requests}';

    protected $description = 'Import past questions from questions.aloc.com.ng into the database';

    // Your DB subject IDs
    protected array $subjectMap = [
        'english'     => 2,   // English Language
        'mathematics' => 1,   // Mathematics
        'chemistry'   => 3,   // Chemistry
        'biology'     => 4,   // Biology
        'physics'     => 5,   // Physics
        'economics'   => 13,  // Economics
        'government'  => 8,   // Government
        'englishlit'  => 7,   // Literature in English
        'accounting'  => 14,  // Accounting
        'commerce'    => 15,  // Commerce
        'geography'   => 11,  // Geography
        'crk'         => 9,   // CRK
        'history'     => 10,  // History
        'insurance'   => 22,  // Insurance
    ];

    // What slug to send to ALOC API — only needed when different from our key
    protected array $alocSlugMap = [
        'englishlit' => 'englishlit',
        'crk'        => 'crk',
        'history'    => 'history',
        'insurance'  => 'insurance',
    ];

    public function handle(): void
    {
        $subject = strtolower($this->argument('subject'));
        $type    = strtolower($this->option('type'));
        $delay   = (int) $this->option('delay');
        $token   = config('services.aloc.token');

        if (!isset($this->subjectMap[$subject])) {
            $this->error("Unknown subject: {$subject}");
            $this->line('Available: ' . implode(', ', array_keys($this->subjectMap)));
            return;
        }

        if (!$token) {
            $this->error('ALOC token not set. Add ALOC_TOKEN=your-token to your .env file.');
            return;
        }

        $subjectId = $this->subjectMap[$subject];
        $alocSlug  = $this->alocSlugMap[$subject] ?? $subject;
        $examType  = strtoupper($type === 'utme' ? 'JAMB' : ($type === 'wassce' ? 'WAEC' : 'Post-UTME'));

        $this->info("Importing {$examType} — {$subject} (sending '{$alocSlug}' to ALOC)");
        $this->newLine();

        $totalSaved   = 0;
        $totalSkipped = 0;
        $totalFailed  = 0;

        try {
            $response = Http::timeout(100)
                ->withHeaders([
                    'AccessToken' => $token,
                    'Accept'      => 'application/json',
                ])
                ->get('https://questions.aloc.com.ng/api/v2/m/40', [
                    'subject' => $alocSlug,
                    'type'    => $type,
                ]);

            if (!$response->successful()) {
                $this->error("HTTP {$response->status()} — request failed");
                $this->line("Response body: " . $response->body());
                return;
            }

            $body      = $response->json();
            $questions = $body['data'] ?? [];

            if (empty($questions)) {
                $this->warn("No questions returned from ALOC for '{$alocSlug}' ({$type}).");
                $this->line("Full response: " . json_encode($body));
                return;
            }

            $this->line("Got " . count($questions) . " questions — saving...");

            foreach ($questions as $q) {
                $questionText = trim($q['question'] ?? '');
                if (empty($questionText)) continue;

                // Duplicate check — same subject + question text + exam type
                $exists = DB::table('questions')
                    ->where('subject_id', $subjectId)
                    ->where('question_text', $questionText)
                    ->where('exam_type', $examType)
                    ->exists();

                if ($exists) {
                    $totalSkipped++;
                    continue;
                }

                // Only save valid 4-digit years
                $rawYear = $q['examyear'] ?? null;
                $year    = (is_numeric($rawYear) && strlen((string)(int)$rawYear) === 4)
                    ? (int)$rawYear
                    : null;

                DB::table('questions')->insert([
                    'subject_id'     => $subjectId,
                    'topic_id'       => null,
                    'exam_type'      => $examType,
                    'year'           => $year,
                    'question_text'  => $questionText,
                    'option_a'       => trim($q['option']['a'] ?? ''),
                    'option_b'       => trim($q['option']['b'] ?? ''),
                    'option_c'       => trim($q['option']['c'] ?? ''),
                    'option_d'       => trim($q['option']['d'] ?? ''),
                    'correct_answer' => strtoupper(trim($q['answer'] ?? '')),
                    'explanation'    => !empty($q['solution']) ? trim($q['solution']) : null,
                    'difficulty'     => 'medium',
                    'image_url'      => !empty($q['image']) ? $q['image'] : null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);

                $totalSaved++;
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $totalFailed++;
        }

        $this->newLine();
        $this->info("Done — {$subject} ({$examType})");
        $this->table(
            ['Saved', 'Skipped (duplicates)', 'Failed'],
            [[$totalSaved, $totalSkipped, $totalFailed]]
        );

        if ($delay > 0) sleep($delay);
    }
}