<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use OpenAI;

class ClassifyQuestions extends Command
{
    protected $signature   = 'classify:questions {subject} {--batch=10} {--limit=500}';
    protected $description = 'Batch classify questions into topics using AI';

    // Map command arg to subject_id
  // Map command arg to subject_id
protected array $subjectMap = [
    'mathematics' => 1,
    'english'     => 2,
    'chemistry'   => 3,
    'biology'     => 4,
    'physics'     => 5,
    'government'  => 8,
    'geography'   => 11,
    'economics'   => 13,
    'commerce'    => 15,

    // ✅ NEW SUBJECTS
    'accounting'  => 14,
    'crk'         => 9,
    'englishlit'  => 7,
    'history'     => 10,
    'insurance'   => 22,
];

    public function handle(): void
    {
        $subjectArg = strtolower($this->argument('subject'));
        $batchSize  = (int) $this->option('batch');
        $limit      = (int) $this->option('limit');

        if (!isset($this->subjectMap[$subjectArg])) {
            $this->error("Unknown subject: {$subjectArg}");
            $this->line('Available: ' . implode(', ', array_keys($this->subjectMap)));
            return;
        }

        $subjectId = $this->subjectMap[$subjectArg];
        $subject   = Subject::find($subjectId);

        if (!$subject) {
            $this->error("Subject not found in DB.");
            return;
        }

        // Get all topics for this subject
        $topics = Topic::where('subject_id', $subjectId)
            ->orderBy('order')
            ->pluck('name', 'id')
            ->toArray();

        if (empty($topics)) {
            $this->error("No topics seeded for {$subject->name}. Run the seeder first.");
            return;
        }

        $this->info("Classifying {$subject->name} questions into " . count($topics) . " topics");
        $this->newLine();

        // Get unclassified questions only
        $questions = Question::where('subject_id', $subjectId)
            ->whereNull('topic_id')
            ->limit($limit)
            ->get(['id', 'question_text', 'exam_type']);

        if ($questions->isEmpty()) {
            $this->info('All questions already classified!');
            return;
        }

        $this->info("Found {$questions->count()} unclassified questions. Processing in batches of {$batchSize}...");
        $this->newLine();

        $client     = OpenAI::client(config('services.openai.api_key'));
        $topicList  = implode("\n", array_map(fn($id, $name) => "- {$name}", array_keys($topics), $topics));
        $topicNames = array_values($topics);

        $batches      = $questions->chunk($batchSize);
        $bar          = $this->output->createProgressBar($questions->count());
        $bar->start();

        $totalClassified = 0;
        $totalFailed     = 0;

        foreach ($batches as $batch) {
            try {
                $result = $this->classifyBatch($client, $batch, $subject->name, $topicList, $topicNames, $topics);
                $totalClassified += $result['classified'];
                $totalFailed     += $result['failed'];
                $bar->advance($batch->count());
                usleep(500000); // 0.5s between batches to avoid rate limits
            } catch (\Exception $e) {
                $this->newLine();
                $this->warn("Batch error: " . $e->getMessage());
                $totalFailed += $batch->count();
                $bar->advance($batch->count());
            }
        }

        $bar->finish();
        $this->newLine();
        $this->newLine();

        // Final stats
        $remaining = Question::where('subject_id', $subjectId)->whereNull('topic_id')->count();
        $this->info("✅ Done!");
        $this->table(
            ['Classified', 'Failed/Skipped', 'Still Unclassified'],
            [[$totalClassified, $totalFailed, $remaining]]
        );
    }

    private function classifyBatch($client, $batch, string $subjectName, string $topicList, array $topicNames, array $topicsById): array
    {
        // Build numbered question list for the batch
        $questionLines = $batch->values()->map(fn($q, $i) =>
            ($i + 1) . ". " . strip_tags($q->question_text)
        )->implode("\n");

        $prompt = "You are classifying {$subjectName} exam questions into topics.\n\n"
            . "Topics (pick ONLY from this list):\n{$topicList}\n\n"
            . "Questions:\n{$questionLines}\n\n"
            . "Reply with ONLY a JSON array of topic names, one per question, in the same order.\n"
            . "Example: [\"Topic Name\", \"Topic Name\", \"Topic Name\"]\n"
            . "Use exact topic names from the list. No explanation, no extra text.";

        $response = $client->chat()->create([
            'model'       => 'gpt-4.1-mini',
            'temperature' => 0.1,
            'max_tokens'  => 500,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => "You classify exam questions into topics. Reply only with a valid JSON array. No markdown, no explanation."
                ],
                [
                    'role'    => 'user',
                    'content' => $prompt
                ]
            ],
        ]);

        $raw = trim($response->choices[0]->message->content ?? '');

        // Strip markdown code fences if present
        $raw = preg_replace('/^```json?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);
        $raw = trim($raw);

        $assignments = json_decode($raw, true);

        if (!is_array($assignments)) {
            return ['classified' => 0, 'failed' => $batch->count()];
        }

        $classified = 0;
        $failed     = 0;
        $questions  = $batch->values();

        foreach ($assignments as $i => $topicName) {
            if (!isset($questions[$i])) break;

            $question  = $questions[$i];
            $topicName = trim($topicName);

            // Find topic_id by name (case insensitive)
            $topicId = collect($topicsById)->first(fn($name) =>
                strtolower($name) === strtolower($topicName)
            );

            // If exact match not found try partial match
            if (!$topicId) {
                $topicId = collect($topicsById)->first(fn($name) =>
                    str_contains(strtolower($name), strtolower($topicName)) ||
                    str_contains(strtolower($topicName), strtolower($name))
                );
            }

            if ($topicId !== null) {
                // Get the actual topic_id key
                $actualTopicId = array_search($topicId, $topicsById);
                if ($actualTopicId) {
                    Question::where('id', $question->id)->update(['topic_id' => $actualTopicId]);
                    $classified++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        return ['classified' => $classified, 'failed' => $failed];
    }
}