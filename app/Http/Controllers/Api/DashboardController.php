<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuizSession;
use App\Models\UserSubjectPerformance;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. STREAK — check if studied today, update streak
        $streak = $this->calculateStreak($user);

        // 2. OVERALL ACCURACY
        $perfData = UserSubjectPerformance::where('user_id', $user->id)
            ->whereNull('topic_id')
            ->get();

        $totalAnswered  = $perfData->sum('total_answered');
        $totalCorrect   = $perfData->sum('total_correct');
        $overallAccuracy = $totalAnswered > 0
            ? round(($totalCorrect / $totalAnswered) * 100)
            : null;

        // 3. ESTIMATED JAMB SCORE (out of 400, 4 subjects x 100)
        $estimatedScore = $this->calculateEstimatedScore($user->id, $user->stream);

        // 4. RECENT SESSIONS — last 3 completed
        $recentSessions = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('subject:id,name,icon')
            ->orderByDesc('completed_at')
            ->limit(3)
            ->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'subject_name'     => $s->subject->name ?? '--',
                'subject_icon'     => $s->subject->icon ?? '📚',
                'score_percentage' => $s->score_percentage,
                'correct'          => $s->correct,
                'total'            => $s->total_questions,
                'mode'             => $s->mode,
                'completed_at'     => $s->completed_at?->diffForHumans(),
            ]);

        // 5. WEAKEST SUBJECT — for AI tip
        $weakest = UserSubjectPerformance::where('user_id', $user->id)
            ->whereNull('topic_id')
            ->where('total_answered', '>=', 5)
            ->orderBy('accuracy')
            ->with('subject:id,name')
            ->first();

        // 6. AI DAILY TIP
        $aiTip = $this->getDailyTip($user, $weakest, $overallAccuracy);

        return response()->json([
            'success'          => true,
            'streak'           => $streak,
            'total_answered'   => $totalAnswered,
            'overall_accuracy' => $overallAccuracy,
            'estimated_score'  => $estimatedScore,
            'recent_sessions'  => $recentSessions,
            'ai_tip'           => $aiTip,
            'weakest_subject'  => $weakest?->subject?->name,
            'weakest_subject_id'  => $weakest?->subject_id,
        ]);
    }

  private function calculateStreak($user): int
{
    // Get all distinct days the user studied, most recent first
    $studyDays = QuizSession::where('user_id', $user->id)
        ->where('status', 'completed')
        ->selectRaw('DATE(completed_at) as study_date')
        ->groupBy('study_date')
        ->orderByDesc('study_date')
        ->pluck('study_date')
        ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
        ->toArray();

    if (empty($studyDays)) {
        $user->update(['streak_days' => 0]);
        return 0;
    }

    $today     = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();

    // If most recent study day is not today or yesterday — streak is broken
    if ($studyDays[0] !== $today && $studyDays[0] !== $yesterday) {
        $user->update(['streak_days' => 0]);
        return 0;
    }

    // Count consecutive days going backwards
    $streak      = 0;
    $compareDate = $studyDays[0] === $today ? $today : $yesterday;

    foreach ($studyDays as $day) {
        if ($day === $compareDate) {
            $streak++;
            $compareDate = \Carbon\Carbon::parse($compareDate)->subDay()->toDateString();
        } else {
            break; // gap found — stop counting
        }
    }

    $user->update(['streak_days' => $streak]);
    return $streak;
}

    private function calculateEstimatedScore($userId, $stream): ?int
    {
        // JAMB is 4 subjects x 100 marks each = 400 total
        // Map stream to core JAMB subjects
        $coreSubjects = match($stream) {
            'science'    => ['English', 'Mathematics', 'Physics', 'Chemistry'],
            'arts'       => ['English', 'Mathematics', 'Literature', 'Government'],
            'commercial' => ['English', 'Mathematics', 'Economics', 'Accounting'],
            default      => ['English', 'Mathematics'],
        };

        $performances = UserSubjectPerformance::where('user_id', $userId)
            ->whereNull('topic_id')
            ->whereHas('subject', fn($q) => $q->whereIn('name', $coreSubjects))
            ->with('subject:id,name')
            ->get();

        if ($performances->isEmpty()) return null;

        $totalScore = 0;
        $subjectCount = 0;

        foreach ($performances as $perf) {
            // Each subject is out of 100 in JAMB
            $subjectScore = round(($perf->accuracy / 100) * 100);
            $totalScore  += $subjectScore;
            $subjectCount++;
        }

        // Scale to however many subjects have data
        // Full score only when all 4 subjects have data
        if ($subjectCount < count($coreSubjects)) {
            // Estimate remaining subjects at 40% (average Nigerian student)
            $remaining    = count($coreSubjects) - $subjectCount;
            $totalScore  += $remaining * 40;
        }

        return min($totalScore, 400);
    }

   private function getDailyTip($user, $weakest, $accuracy): string
{
    // No data yet — generic tip
    if (!$weakest && $accuracy === null) {
        return "Start practicing today! Even 10 questions a day will move your JAMB score significantly over 6 weeks.";
    }

    // Cache tip per user per day
    $cacheKey  = "ai_tip_{$user->id}_" . now()->toDateString();
    $cachedTip = cache($cacheKey);
    if ($cachedTip) return $cachedTip;

    try {
        $client = OpenAI::client(config('services.openai.api_key'));

        // Build context from subject-level performance only
        $perfSummary = UserSubjectPerformance::where('user_id', $user->id)
            ->whereNull('topic_id')
            ->where('total_answered', '>=', 5)
            ->with('subject:id,name')
            ->orderBy('accuracy')
            ->get()
            ->map(fn($p) => "{$p->subject->name}: {$p->accuracy}% accuracy ({$p->total_answered} questions)")
            ->implode(', ');

        $context = $perfSummary
            ? "Student's subject performance — {$perfSummary}."
            : "Student has {$accuracy}% overall accuracy across all subjects.";

        $response = $client->chat()->create([
            'model'       => 'gpt-4.1-mini',
            'temperature' => 0.7,
            'max_tokens'  => 80,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => 'You are a Nigerian JAMB coach. Give one short, specific, actionable study tip based on the student performance data. No greetings. Max 2 sentences. Speak directly to the student as "you".'
                ],
                [
                    'role'    => 'user',
                    'content' => $context . " Give them one tip for today."
                ]
            ],
        ]);

        $tip = trim($response->choices[0]->message->content ?? '');

        cache([$cacheKey => $tip], now()->addHours(24));

        return $tip ?: "Focus on your weakest subject today — 20 targeted questions beats 100 random ones.";

    } catch (\Exception $e) {
        return "Focus on your weakest subject today — 20 targeted questions beats 100 random ones.";
    }
}
}