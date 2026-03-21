<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subject;
use App\Models\Question;
use App\Models\UserSubjectPerformance;
use Illuminate\Http\Request;
use OpenAI;

class TopicController extends Controller
{
    /**
     * Get all topics for a subject with user accuracy per topic.
     * GET /api/subjects/{subjectId}/topics
     */
    public function index(Request $request, $subjectId)
    {
        $user = $request->user();

        $topics = Topic::where('subject_id', $subjectId)
            ->withCount('questions')
            ->orderBy('order')
            ->get();

        if ($topics->isEmpty()) {
            return response()->json([
                'success' => true,
                'topics'  => [],
            ]);
        }

        // Get user's accuracy per topic for this subject
        $performance = UserSubjectPerformance::where('user_id', $user->id)
            ->where('subject_id', $subjectId)
            ->whereNotNull('topic_id')
            ->pluck('accuracy', 'topic_id')
            ->toArray();

        $mapped = $topics->map(fn($t) => [
            'id'             => $t->id,
            'name'           => $t->name,
            'question_count' => $t->questions_count,
            'has_notes'      => !empty($t->summary),
            'accuracy'       => isset($performance[$t->id]) ? (int) $performance[$t->id] : null,
        ]);

        return response()->json([
            'success' => true,
            'topics'  => $mapped,
        ]);
    }

    /**
     * Get or generate AI notes for a topic.
     * GET /api/topics/{topicId}/notes
     * Premium only.
     */
    public function notes(Request $request, $topicId)
    {
        $user  = $request->user();
        $topic = Topic::with('subject')->findOrFail($topicId);

        // Premium gate
        // if (!$user->isPremium()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'premium_required',
        //     ], 403);
        // }

        // Return cached notes instantly
        if (!empty($topic->summary)) {
            return response()->json([
                'success'  => true,
                'topic'    => $topic->name,
                'subject'  => $topic->subject->name,
                'notes'    => $topic->summary,
                'cached'   => true,
            ]);
        }

        // Generate fresh notes
        $notes = $this->generateNotes($topic);

        return response()->json([
            'success' => true,
            'topic'   => $topic->name,
            'subject' => $topic->subject->name,
            'notes'   => $notes,
            'cached'  => false,
        ]);
    }

    private function generateNotes(Topic $topic): string
    {
        try {
            $subjectName = $topic->subject->name;
            $topicName   = $topic->name;

           // Use seeded subtopics and objectives as context (model casts them to arrays)
$subtopics  = (array) ($topic->subtopics  ?? []);
$objectives = (array) ($topic->objectives ?? []);

// Build text
$subtopicsText  = !empty($subtopics)
    ? implode("\n- ", $subtopics)
    : '';

$objectivesText = !empty($objectives)
    ? implode("\n- ", $objectives)
    : '';
            // Sample past questions for extra context
            $sampleQuestions = Question::where('topic_id', $topic->id)
                ->limit(12)
                ->pluck('question_text')
                ->map(fn($q) => strip_tags($q))
                ->implode("\n- ");

            $client   = OpenAI::client(config('services.openai.api_key'));
            $response = $client->chat()->create([
                'model'       => 'gpt-4.1-mini',
                'temperature' => 0.4,
                'max_tokens'  => 1700,
                'messages'    => [
                    [
                        'role'    => 'system',
                        'content' => 'You are a Nigerian secondary school teacher writing JAMB/WAEC study notes. '
                            . 'Write clear, simple, exam-focused notes. No greetings. Go straight into content. '
                            . 'Use simple English an SS3 student will understand easily. '
                            . 'Cover everything in the subtopics and objectives provided.'
                    ],
                    [
                        'role'    => 'user',
                        'content' => "Write study notes for {$subjectName} — Topic: {$topicName}\n\n"
                            . ($subtopicsText  ? "Subtopics to cover:\n- {$subtopicsText}\n\n"          : '')
                            . ($objectivesText ? "Learning objectives (students must be able to):\n- {$objectivesText}\n\n" : '')
                            . "Structure the notes exactly like this:\n\n"
                            . "📌 SUMMARY\n"
                            . "2-3 sentences explaining what this topic is about.\n\n"
                            . "🔑 KEY POINTS\n"
                            . "6-8 bullet points of the most important facts, definitions and concepts. Cover all subtopics.\n\n"
                            . "⚡ JAMB/WAEC EXAM TRICKS\n"
                            . "3-4 specific tips on how this topic appears in JAMB/WAEC and how to answer correctly.\n\n"
                            . ($sampleQuestions ? "Past exam questions from this topic for context:\n- {$sampleQuestions}" : '')
                            . "give answer and  hint to the past questions"
                            ."let all the notest be summed up to 1700 tokens "
                    ]
                ],
            ]);

            $notes = trim($response->choices[0]->message->content ?? '');

            if (!empty($notes)) {
                $topic->update([
                    'summary'              => $notes,
                    'summary_generated_at' => now(),
                ]);
            }

            return $notes ?: 'Notes not available for this topic yet.';

        } catch (\Exception $e) {
            return 'Could not generate notes. Try again later.';
        }
    }
}