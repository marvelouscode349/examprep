<?php

namespace App\Http\Controllers\Api;

use OpenAI;
use App\Http\Controllers\Controller;
use App\Models\QuizSession;
use App\Models\UserSubjectPerformance;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. OVERALL ACCURACY
        $perf = UserSubjectPerformance::where('user_id', $user->id)
            ->whereNull('topic_id')
            ->get();

        $totalAnswered  = $perf->sum('total_answered');
        $totalCorrect   = $perf->sum('total_correct');
        $overallAccuracy = $totalAnswered > 0
            ? round(($totalCorrect / $totalAnswered) * 100)
            : null;

        // 2. SUBJECT BREAKDOWN
        $subjectBreakdown = UserSubjectPerformance::where('user_id', $user->id)
            ->whereNull('topic_id')
            ->where('total_answered', '>', 0)
            ->with('subject:id,name,icon')
            ->orderBy('accuracy')
            ->get()
            ->map(fn($p) => [
                'id'              => $p->subject->id,      // <= ensure this exists
                'subject_name'    => $p->subject->name ?? '--',
                'subject_icon'    => $p->subject->icon ?? '📚',
                'total_answered'  => $p->total_answered,
                'total_correct'   => $p->total_correct,
                'accuracy'        => $p->accuracy,
                'last_practiced'  => $p->last_practiced_at?->diffForHumans(),
                'status'          => $p->accuracy >= 70 ? 'strong' : ($p->accuracy >= 50 ? 'average' : 'weak'),
            ]);

        // 3. SCORE TREND — last 8 completed sessions
        $sessions = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->limit(8)
            ->get(['id', 'score_percentage', 'completed_at', 'subject_id'])
            ->reverse()
            ->values();

        $scoreTrend = $sessions->map(fn($s) => [
            'score'      => $s->score_percentage,
            'label'      => $s->completed_at->format('d M'),
            'subject_id' => $s->subject_id,
        ]);

        // 4. STREAK GRID — last 35 days
        $streakGrid = $this->buildStreakGrid($user->id);

        // 5. ESTIMATED JAMB SCORE
        $estimatedScore = $this->calculateEstimatedScore($user->id, $user->stream);

        // 6. TOTAL SESSIONS
        $totalSessions = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'success'           => true,
            'overall_accuracy'  => $overallAccuracy,
            'total_answered'    => $totalAnswered,
            'total_sessions'    => $totalSessions,
            'estimated_score'   => $estimatedScore,
            'subject_breakdown' => $subjectBreakdown,
            'score_trend'       => $scoreTrend,
            'streak_grid'       => $streakGrid,
            'streak_days'       => $user->streak_days ?? 0,
        ]);
    }

    private function buildStreakGrid($userId): array
    {
        // Get all days with completed sessions in last 35 days
        $activeDays = QuizSession::where('user_id', $userId)
            ->where('status', 'completed')
            ->where('completed_at', '>=', now()->subDays(34))
            ->selectRaw('DATE(completed_at) as study_date, COUNT(*) as sessions')
            ->groupBy('study_date')
            ->pluck('sessions', 'study_date')
            ->toArray();

        $grid = [];
        for ($i = 34; $i >= 0; $i--) {
            $date      = now()->subDays($i)->toDateString();
            $sessions  = $activeDays[$date] ?? 0;
            $grid[]    = [
                'date'     => $date,
                'level'    => $sessions === 0 ? 0 : ($sessions >= 3 ? 3 : ($sessions >= 2 ? 2 : 1)),
                'sessions' => $sessions,
            ];
        }

        return $grid;
    }

    private function calculateEstimatedScore($userId, $stream): ?int
    {
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

        $totalScore   = 0;
        $subjectCount = 0;

        foreach ($performances as $perf) {
            $totalScore += round(($perf->accuracy / 100) * 100);
            $subjectCount++;
        }

        if ($subjectCount < count($coreSubjects)) {
            $remaining   = count($coreSubjects) - $subjectCount;
            $totalScore += $remaining * 40;
        }

        return min($totalScore, 400);
    }


public function studyPlan(Request $request)
{
    $user = $request->user();

    // ---------- 0) Cached plan? Return if still within 7 days ----------
    $generatedAt = $user->study_plan_generated_at
        ? ($user->study_plan_generated_at instanceof Carbon
            ? $user->study_plan_generated_at
            : Carbon::parse($user->study_plan_generated_at))
        : null;

    $hasPlan   = !empty($user->study_plan) && !empty($generatedAt);
    $isFresh   = $hasPlan && $generatedAt->gt(now()->subDays(7));

    if ($isFresh) {
        return response()->json([
            'success'     => true,
            'cached'      => true,
            'plan'        => $user->study_plan,
            'days_left'   => max(0, 7 - $generatedAt->diffInDays(now())),
            'days_since'  => $generatedAt->diffInDays(now()),
        ]);
    }

    // ---------- 1) First-time eligibility: lifetime answers >= 20 ----------
    $lifetimeAnswered = UserAnswer::where('user_id', $user->id)->count();

    if (!$hasPlan && $lifetimeAnswered < 20) {
        return response()->json([
            'success'  => false,
            'reason'   => 'not_enough_data',
            'message'  => 'You need at least 20 answered questions to generate your first AI study plan.',
            'answered' => $lifetimeAnswered,
        ], 422);
    }

    // ---------- 2) Last 7 days activity (for refresh) ----------
    $sevenDaysAgo    = now()->subDays(7);
    $recentSessions  = QuizSession::where('user_id', $user->id)
        ->where('status', 'completed')
        ->where('completed_at', '>=', $sevenDaysAgo)
        ->pluck('id');

    // Detect session FK on user_answers
    $sessionFk = Schema::hasColumn('user_answers', 'session_id')
        ? 'session_id'
        : (Schema::hasColumn('user_answers', 'quiz_session_id') ? 'quiz_session_id' : null);

    // Count recent answers
    if ($sessionFk) {
        $recentAnswered = UserAnswer::whereIn($sessionFk, $recentSessions)->count();
    } else {
        // Fallback if no FK: use user_id + created_at window
        $recentAnswered = UserAnswer::where('user_id', $user->id)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->count();
    }

    $isRefreshAttempt = $hasPlan && !$isFresh; // has a plan, but it's 7+ days old

    // If this is a REFRESH attempt (plan expired), require >= 20 answers in last 7 days
    if ($isRefreshAttempt && $recentAnswered < 20) {
        return response()->json([
            'success'  => false,
            'reason'   => 'not_enough_recent_data',
            'message'  => 'You need at least 20 questions answered in the last 7 days to refresh your study plan.',
            'answered' => $recentAnswered,
        ], 422);
    }

    // ---------- 3) Build performance context ----------
    // First-time plan => use lifetime answers
    // Refresh plan    => use last 7 days answers only
    $useRecentWindow = $isRefreshAttempt || ($hasPlan && !$isFresh); // any refresh path uses recent

    // SUBJECTS
    if ($useRecentWindow) {
        if ($sessionFk) {
            $bySubject = DB::table('user_answers as ua')
                ->join('quiz_sessions as qs', "qs.id", '=', "ua.$sessionFk")
                ->join('questions as q', 'q.id', '=', 'ua.question_id')
                ->join('subjects as s', 's.id', '=', 'q.subject_id')
                ->where('qs.user_id', $user->id)
                ->where('qs.status', 'completed')
                ->where('qs.completed_at', '>=', $sevenDaysAgo)
                ->select([
                    'q.subject_id',
                    's.name as subject_name',
                    DB::raw('COUNT(*) as answered'),
                    DB::raw("SUM(CASE WHEN UPPER(ua.chosen_answer) = UPPER(q.correct_answer) THEN 1 ELSE 0 END) as correct"),
                ])
                ->groupBy('q.subject_id', 's.name')
                ->get();
        } else {
            $bySubject = DB::table('user_answers as ua')
                ->join('questions as q', 'q.id', '=', 'ua.question_id')
                ->join('subjects as s', 's.id', '=', 'q.subject_id')
                ->where('ua.user_id', $user->id)
                ->where('ua.created_at', '>=', $sevenDaysAgo)
                ->select([
                    'q.subject_id',
                    's.name as subject_name',
                    DB::raw('COUNT(*) as answered'),
                    DB::raw("SUM(CASE WHEN UPPER(ua.chosen_answer) = UPPER(q.correct_answer) THEN 1 ELSE 0 END) as correct"),
                ])
                ->groupBy('q.subject_id', 's.name')
                ->get();
        }
    } else {
        // Lifetime
        $bySubject = DB::table('user_answers as ua')
            ->join('questions as q', 'q.id', '=', 'ua.question_id')
            ->join('subjects as s', 's.id', '=', 'q.subject_id')
            ->where('ua.user_id', $user->id)
            ->select([
                'q.subject_id',
                's.name as subject_name',
                DB::raw('COUNT(*) as answered'),
                DB::raw("SUM(CASE WHEN UPPER(ua.chosen_answer) = UPPER(q.correct_answer) THEN 1 ELSE 0 END) as correct"),
            ])
            ->groupBy('q.subject_id', 's.name')
            ->get();
    }

    // TOPICS
    if ($useRecentWindow) {
        if ($sessionFk) {
            $byTopic = DB::table('user_answers as ua')
                ->join('quiz_sessions as qs', "qs.id", '=', "ua.$sessionFk")
                ->join('questions as q', 'q.id', '=', 'ua.question_id')
                ->join('topics as t', 't.id', '=', 'q.topic_id')
                ->where('qs.user_id', $user->id)
                ->where('qs.status', 'completed')
                ->where('qs.completed_at', '>=', $sevenDaysAgo)
                ->whereNotNull('q.topic_id')
                ->select([
                    'q.topic_id',
                    't.name as topic_name',
                    DB::raw('COUNT(*) as answered'),
                    DB::raw("SUM(CASE WHEN UPPER(ua.chosen_answer) = UPPER(q.correct_answer) THEN 1 ELSE 0 END) as correct"),
                ])
                ->groupBy('q.topic_id', 't.name')
                ->orderBy('answered', 'desc')
                ->limit(50)
                ->get();
        } else {
            $byTopic = DB::table('user_answers as ua')
                ->join('questions as q', 'q.id', '=', 'ua.question_id')
                ->join('topics as t', 't.id', '=', 'q.topic_id')
                ->where('ua.user_id', $user->id)
                ->where('ua.created_at', '>=', $sevenDaysAgo)
                ->whereNotNull('q.topic_id')
                ->select([
                    'q.topic_id',
                    't.name as topic_name',
                    DB::raw('COUNT(*) as answered'),
                    DB::raw("SUM(CASE WHEN UPPER(ua.chosen_answer) = UPPER(q.correct_answer) THEN 1 ELSE 0 END) as correct"),
                ])
                ->groupBy('q.topic_id', 't.name')
                ->orderBy('answered', 'desc')
                ->limit(50)
                ->get();
        }
    } else {
        // Lifetime
        $byTopic = DB::table('user_answers as ua')
            ->join('questions as q', 'q.id', '=', 'ua.question_id')
            ->join('topics as t', 't.id', '=', 'q.topic_id')
            ->where('ua.user_id', $user->id)
            ->whereNotNull('q.topic_id')
            ->select([
                'q.topic_id',
                't.name as topic_name',
                DB::raw('COUNT(*) as answered'),
                DB::raw("SUM(CASE WHEN UPPER(ua.chosen_answer) = UPPER(q.correct_answer) THEN 1 ELSE 0 END) as correct"),
            ])
            ->groupBy('q.topic_id', 't.name')
            ->orderBy('answered', 'desc')
            ->limit(50)
            ->get();
    }

    // Compute weak sets
    $weakSubjects = $bySubject->map(function ($row) {
            $acc = $row->answered > 0 ? round(($row->correct / $row->answered) * 100) : 0;
            return [
                'name'     => $row->subject_name,
                'accuracy' => $acc,
                'answered' => (int) $row->answered,
            ];
        })
        ->filter(fn ($s) => $s['accuracy'] < 50)
        ->values();

    $weakTopics = $byTopic->map(function ($row) {
            $acc = $row->answered > 0 ? round(($row->correct / $row->answered) * 100) : 0;
            return [
                'name'      => $row->topic_name,
                'accuracy'  => $acc,
                'answered'  => (int) $row->answered,
            ];
        })
        ->filter(fn ($t) => $t['accuracy'] < 50 && $t['answered'] > 0)
        ->sortBy('accuracy')
        ->take(12)
        ->values();

    // For prompt: total answered used in this window
    $totalUsed = $useRecentWindow ? $recentAnswered : $lifetimeAnswered;

    // ---------- 4) Call OpenAI ----------
    try {
        $client   = OpenAI::client(config('services.openai.api_key'));
        $response = $client->chat()->create([
            'model'       => 'gpt-4.1-mini',
            'temperature' => 0.4,
            'max_tokens'  => 900,
            'messages'    => [
                [
                    'role'    => 'system',
                    'content' => "You are a Nigerian JAMB/WAEC tutor. Create a practical 7-day study plan.\n"
                        . "Use plain text (no markdown symbols). Short bullet points. Simple English.\n"
                        . "Focus on weak subjects and weak topics based on the provided performance.\n"
                        . "Include each day's: quick revision, main practice focus, short notes/flashcards, target number of questions, and a motivation tip."
                ],
                [
                    'role'    => 'user',
                    'content' =>
                        "Total answered considered: {$totalUsed}\n"
                        . "Window: " . ($useRecentWindow ? "last 7 days only" : "lifetime") . "\n\n"
                        . "Weak Subjects:\n"
                        . ($weakSubjects->isEmpty()
                            ? "- None\n"
                            : $weakSubjects->map(fn($s) =>
                                "- {$s['name']} ({$s['accuracy']}% accuracy, {$s['answered']} attempted)"
                              )->implode("\n"))
                        . "\n\nWeak Topics:\n"
                        . ($weakTopics->isEmpty()
                            ? "- None\n"
                            : $weakTopics->map(fn($t) =>
                                "- {$t['name']} ({$t['accuracy']}% accuracy, {$t['answered']} questions)"
                              )->implode("\n"))
                        . "\n\nCreate the 7-day plan now."
                ],
            ],
        ]);

        $plan = trim($response->choices[0]->message->content ?? '');

        if ($plan === '') {
            return response()->json([
                'success' => false,
                'reason'  => 'ai_empty',
                'message' => 'AI returned an empty plan.',
            ], 500);
        }

        // Cache for 7 days
        $user->update([
            'study_plan'              => $plan,
            'study_plan_generated_at' => now(),
        ]);

        return response()->json([
            'success'     => true,
            'cached'      => false,
            'plan'        => $plan,
            'days_left'   => 7,
            'days_since'  => 0,
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'reason'  => 'ai_failed',
            'message' => 'AI could not generate a study plan. Try again later.',
        ], 500);
    }
}

public function studyPlanStatus(Request $request)
{
    $user = $request->user();

    $hasPlan   = !empty($user->study_plan) && !empty($user->study_plan_generated_at);
    $generated = $user->study_plan_generated_at
        ? ($user->study_plan_generated_at instanceof Carbon
            ? $user->study_plan_generated_at
            : Carbon::parse($user->study_plan_generated_at))
        : null;

    $within7Days = $hasPlan && $generated && $generated->gt(now()->subDays(7));
    $daysSince   = $generated ? $generated->diffInDays(now()) : null;
    $daysLeft    = $within7Days ? max(0, 7 - $daysSince) : 0;

    return response()->json([
        'success'     => true,
        'has_plan'    => $hasPlan,
        'cached'      => $within7Days,
        'updated_at'  => $generated?->toIso8601String(),
        'days_since'  => $daysSince,
        'days_left'   => $daysLeft,
    ]);
}
}