<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportAlocQuestions extends Command
{
    protected $signature = 'import:aloc
                            {subject : e.g. chemistry, mathematics, english}
                            {--type=utme : utme, wassce or post-utme}
                            {--delay=1 : seconds between requests}';

    protected $description = 'Import past questions from questions.aloc.com.ng into the database';

    protected array $subjectMap = [
        'english'     => 2,
        'mathematics' => 1,
        'chemistry'   => 3,
        'biology'     => 4,
        'physics'     => 5,
        'economics'   => 13,
        'government'  => 8,
        'literature'  => 7,
        'accounting'  => 14,
        'commerce'    => 15,
        'geography'   => 11,
        'crk'         => 9,
    ];

    public function handle(): void
    {
        $subject   = strtolower($this->argument('subject'));
        $type      = strtolower($this->option('type'));
        $delay     = (int) $this->option('delay');
        $token     = config('services.aloc.token');

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

        $this->info("Importing {$type} {$subject}");
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
                    'subject' => $subject,
                    'type'    => $type,
                ]);

            if (!$response->successful()) {
                $this->error("HTTP {$response->status()} — request failed");
                return;
            }

            $body      = $response->json();
            $questions = $body['data'] ?? [];

            if (empty($questions)) {
                $this->line("No questions returned.");
                return;
            }

            $this->line("Got " . count($questions) . " questions — saving...");

            foreach ($questions as $q) {
                $questionText = trim($q['question'] ?? '');
                if (empty($questionText)) continue;

      $examType = strtoupper($type === 'utme' ? 'JAMB' : ($type === 'wassce' ? 'WAEC' : 'Post-UTME'));

$exists = DB::table('questions')
    ->where('subject_id', $subjectId)
    ->where('question_text', $questionText)
    ->where('exam_type', $examType)
    ->exists();

                if ($exists) {
                    $totalSkipped++;
                    continue;
                }

                DB::table('questions')->insert([
                    'subject_id'     => $subjectId,
                    'topic_id'       => null,
                    'exam_type'      => $examType,
                    'year'           => (int) ($q['examyear'] ?? 0),
                    'question_text'  => $questionText,
                    'option_a'       => trim($q['option']['a'] ?? ''),
                    'option_b'       => trim($q['option']['b'] ?? ''),
                    'option_c'       => trim($q['option']['c'] ?? ''),
                    'option_d'       => trim($q['option']['d'] ?? ''),
                    'correct_answer' => strtoupper(trim($q['answer'] ?? '')),
                    'explanation'    => trim($q['solution'] ?? ''),
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
        $this->info("Done!");
        $this->table(
            ['Saved', 'Skipped (duplicates)', 'Failed'],
            [[$totalSaved, $totalSkipped, $totalFailed]]
        );
    }
}