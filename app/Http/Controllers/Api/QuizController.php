<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuizSession;
use App\Models\UserAnswer;
use App\Models\UserSubjectPerformance;
use Illuminate\Http\Request;
use OpenAI;

class QuizController extends Controller
{
    /**
     * Start a new quiz session.
     * POST /api/quiz/start
     *
     * Body: {
     *   subject_id: 3,
     *   mode: "practice",       // practice | mock | weak_areas | topic
     *   exam_type: "JAMB",
     *   total_questions: 20,
     *   topic_id: null          // optional, for topic mode
     * }
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'subject_id'      => 'required|exists:subjects,id',
            'mode'            => 'required|in:practice,mock,weak_areas,topic',
            'exam_type'       => 'nullable|in:JAMB,WAEC,NECO,Post-UTME',
            'total_questions' => 'nullable|integer|min:5|max:100',
            'topic_id'        => 'nullable|exists:topics,id',
        ]);

        $user            = $request->user();
        $subjectId       = $validated['subject_id'];
        $mode            = $validated['mode'];
        $examType        = $validated['exam_type'] ?? 'JAMB';
        $totalQuestions  = $validated['total_questions'] ?? 20;
        $topicId         = $validated['topic_id'] ?? null;

        // Build question query based on mode
        $query = Question::where('subject_id', $subjectId)
            ->where('exam_type', $examType);

        if ($mode === 'topic' && $topicId) {
            $query->where('topic_id', $topicId);
        }

       if ($mode === 'weak_areas') {
    // Try topic-level weak areas first
    $weakTopicIds = UserSubjectPerformance::where('user_id', $user->id)
        ->where('subject_id', $subjectId)
        ->where('accuracy', '<', 50)
        ->whereNotNull('topic_id')
        ->pluck('topic_id');

    if ($weakTopicIds->isNotEmpty()) {
        // Topic-level weak areas found — use them
        $query->whereIn('topic_id', $weakTopicIds);
    } else {
        // No topic data — fall back to subject-level
        // Get subjects where user accuracy is below their overall average
        $overallAccuracy = UserSubjectPerformance::where('user_id', $user->id)
            ->whereNull('topic_id')
            ->avg('accuracy') ?? 50;

        $subjectPerf = UserSubjectPerformance::where('user_id', $user->id)
            ->where('subject_id', $subjectId)
            ->whereNull('topic_id')
            ->first();

        // If subject accuracy is below overall or below 50, use all questions
        // Just randomize — no specific filter at subject level
        // The mode still works, just random questions from the subject
    }
}

        // Get random questions
        $questions = $query->inRandomOrder()
            ->limit($totalQuestions)
            ->get(['id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'exam_type', 'year', 'correct_answer','image_url']);

        if ($questions->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No questions found for this selection.'
            ], 404);
        }

        // Create the session
        $session = QuizSession::create([
            'user_id'         => $user->id,
            'subject_id'      => $subjectId,
            'mode'            => $mode,
            'exam_type'       => $examType,
            'total_questions' => $questions->count(),
            'status'          => 'active',
            'started_at'      => now(),
        ]);

        return response()->json([
            'success'    => true,
            'session_id' => $session->id,
            'questions'  => $questions,
            'total'      => $questions->count(),
        ]);
    }

    /**
     * Submit a single answer.
     * POST /api/quiz/submit
     *
     * Body: {
     *   session_id: 1,
     *   question_id: 301,
     *   chosen_answer: "B",
     *   time_spent: 18       // seconds spent on this question
     * }
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'session_id'    => 'required|exists:quiz_sessions,id',
            'question_id'   => 'required|exists:questions,id',
            'chosen_answer' => 'nullable|in:A,B,C,D',
            'time_spent'    => 'nullable|integer|min:0',
        ]);

        $user      = $request->user();
        $session   = QuizSession::where('id', $validated['session_id'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $question  = Question::findOrFail($validated['question_id']);
        $chosen    = strtoupper($validated['chosen_answer'] ?? '');
        $correct   = strtoupper($question->correct_answer);
        $isCorrect = $chosen === $correct;

        // Save the answer
        UserAnswer::create([
            'user_id'         => $user->id,
            'quiz_session_id' => $session->id,
            'question_id'     => $question->id,
            'chosen_answer'   => $chosen ?: null,
            'correct_answer'  => $correct,
            'is_correct'      => $isCorrect,
            'time_spent'      => $validated['time_spent'] ?? 0,
        ]);

        // Build response — only send explanation if wrong
        $response = [
            'success'        => true,
            'is_correct'     => $isCorrect,
            'correct_answer' => $correct,
        ];

        if (!$isCorrect) {

         $explanation = $question->explanation;

    // If no explanation exists, generate one with AI and cache it
    if (empty($explanation)) {
        $explanation = $this->generateAndCacheExplanation($question);
    }
            $response['explanation'] = $question->explanation;
            $response['option_a']    = $question->option_a;
            $response['option_b']    = $question->option_b;
            $response['option_c']    = $question->option_c;
            $response['option_d']    = $question->option_d;
        }

        return response()->json($response);
    }

    /**
     * Finish a quiz session — calculates final score and updates performance.
     * POST /api/quiz/session/{id}/finish
     *
     * Body: { time_taken: 1080 }  // total seconds
     */
    public function finish(Request $request, $sessionId)
    {
        $user    = $request->user();
        $session = QuizSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->firstOrFail();

        $timeTaken = $request->input('time_taken', 0);

        // Calculate score from saved answers
        $answers  = UserAnswer::where('quiz_session_id', $session->id)->get();
        $correct  = $answers->where('is_correct', true)->count();
        $wrong    = $answers->where('is_correct', false)->whereNotNull('chosen_answer')->count();
        $skipped  = $answers->whereNull('chosen_answer')->count();
        $total    = $answers->count();
        $percent  = $total > 0 ? round(($correct / $total) * 100) : 0;

        // Update session
        $session->update([
            'correct'          => $correct,
            'wrong'            => $wrong,
            'skipped'          => $skipped,
            'score_percentage' => $percent,
            'time_taken'       => $timeTaken,
            'status'           => 'completed',
            'completed_at'     => now(),
        ]);

        // Update user subject performance
        $this->updatePerformance($user->id, $session->subject_id, $answers);

        return response()->json([
            'success'          => true,
            'session_id'       => $session->id,
            'correct'          => $correct,
            'wrong'            => $wrong,
            'skipped'          => $skipped,
            'total'            => $total,
            'score_percentage' => $percent,
            'time_taken'       => $timeTaken,
        ]);
    }

    /**
     * Get session review — wrong answers with explanations.
     * GET /api/quiz/session/{id}/review
     */
    public function review(Request $request, $sessionId)
    {
        $user    = $request->user();
        $session = QuizSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $wrongAnswers = UserAnswer::where('quiz_session_id', $session->id)
            ->where('is_correct', false)
            ->with('question')
            ->get()
            ->map(function ($answer) {
                return [
                    'question_id'    => $answer->question_id,
                    'question_text'  => $answer->question->question_text,
                    'option_a'       => $answer->question->option_a,
                    'option_b'       => $answer->question->option_b,
                    'option_c'       => $answer->question->option_c,
                    'option_d'       => $answer->question->option_d,
                    'chosen_answer'  => $answer->chosen_answer,
                    'correct_answer' => $answer->correct_answer,
                    'explanation'    => $answer->question->explanation,
                ];
            });

        return response()->json([
            'success'       => true,
            'session_id'    => $session->id,
            'wrong_answers' => $wrongAnswers,
            'total_wrong'   => $wrongAnswers->count(),
        ]);
    }

    /**
     * Update user_subject_performance after a session finishes.
     * Called internally from finish().
     */
    private function updatePerformance($userId, $subjectId, $answers): void
    {
        foreach ($answers as $answer) {
            $question = Question::find($answer->question_id);
            if (!$question) continue;

            $topicId = $question->topic_id;

            // Update subject-level performance (topic_id = null)
            $this->upsertPerformance($userId, $subjectId, null, $answer->is_correct);

            // Update topic-level performance if topic exists
            if ($topicId) {
                $this->upsertPerformance($userId, $subjectId, $topicId, $answer->is_correct);
            }
        }
    }

    private function upsertPerformance($userId, $subjectId, $topicId, bool $isCorrect): void
    {
        $perf = UserSubjectPerformance::firstOrCreate(
            [
                'user_id'    => $userId,
                'subject_id' => $subjectId,
                'topic_id'   => $topicId,
            ],
            [
                'total_answered' => 0,
                'total_correct'  => 0,
                'accuracy'       => 0,
            ]
        );

        $perf->total_answered    += 1;
        $perf->total_correct     += $isCorrect ? 1 : 0;
        $perf->accuracy          = round(($perf->total_correct / $perf->total_answered) * 100);
        $perf->last_practiced_at = now();
        $perf->save();
    }

private function generateAndCacheExplanation(Question $question): string
{
    try {
        $client = OpenAI::client(config('services.openai.api_key'));
        $response = $client->chat()->create([
            'model'       => 'gpt-4.1-mini',
            'temperature' => 0.4,
            'max_tokens'  => 250,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => 'You are a straight-talking Nigerian teacher. Never use greetings, intros or filler phrases like "Alright class", "Great question", "Let us explore". Always go straight into the explanation. Be concise.'
                ],
                [
                    'role'    => 'user',
                    'content' => "Explain this {$question->exam_type} question to a secondary school student.\n\n"
                        . "Question: {$question->question_text}\n"
                        . "A: {$question->option_a}\n"
                        . "B: {$question->option_b}\n"
                        . "C: {$question->option_c}\n"
                        . "D: {$question->option_d}\n"
                        . "Correct Answer: {$question->correct_answer}\n\n"
                        . "3-4 sentences. Why each wrong option fails in one phrase each. Why correct answer is right. End with a real life Nigerian example or memory trick they won't forget."
                ]
            ],
        ]);

        $explanation = trim($response->choices[0]->message->content ?? '');

        if (!empty($explanation)) {
            $question->update(['explanation' => $explanation]);
        }

        return $explanation;

    } catch (\Exception $e) {
        return 'Explanation not available for this question yet.';
    }
}

public function getExplanation(Request $request, $questionId)
{
    $question = Question::findOrFail($questionId);

    if (empty($question->explanation)) {
        $explanation = $this->generateAndCacheExplanation($question);
    } else {
        $explanation = $question->explanation;
    }

    return response()->json([
        'success'     => true,
        'explanation' => $explanation,
    ]);
}

public function streamExplanation(Request $request, $questionId)
{
    $question = Question::findOrFail($questionId);

    // Return cached explanation instantly
    if (!empty($question->explanation)) {
        return response()->json([
            'success'     => true,
            'explanation' => $question->explanation,
            'cached'      => true
        ]);
    }

    // Generate fresh explanation
    $explanation = $this->generateAndCacheExplanation($question);

    return response()->json([
        'success'     => true,
        'explanation' => $explanation,
        'cached'      => false
    ]);
}
}