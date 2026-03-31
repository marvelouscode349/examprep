<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\QuizSession;
use App\Models\Subscription;
use App\Models\DiscountCode;
use App\Models\Marketer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // ============================================================
    // DASHBOARD OVERVIEW
    // ============================================================
   public function dashboard()
{
    $stats = [
        'total_users'     => User::count(),
        'premium_users'   => User::where('subscription_status', 'active')->count(),
        'free_users'      => User::where(function($q) {
                                $q->where('subscription_status', '!=', 'active')
                                  ->orWhereNull('subscription_status');
                            })->count(),
        'total_questions' => Question::count(),
        'total_sessions'  => QuizSession::where('status', 'completed')->count(),
        'sessions_today'  => QuizSession::where('status', 'completed')
                                ->whereDate('completed_at', today())->count(),
        'new_users_today' => User::whereDate('created_at', today())->count(),
        'new_users_week'  => User::where('created_at', '>=', now()->subDays(7))->count(),
        'total_revenue'   => 0, // wire up when Paystack is done
        'revenue_month'   => 0, // wire up when Paystack is done
    ];

    $planBreakdown = collect([]);

    $questionsBySubject = Question::selectRaw('subjects.name, count(*) as total')
        ->join('subjects', 'subjects.id', '=', 'questions.subject_id')
        ->groupBy('subjects.name')
        ->orderByDesc('total')
        ->get();

    $sessionsChart = collect(range(6, 0))->map(function ($daysAgo) {
        $date = now()->subDays($daysAgo);
        return [
            'date'  => $date->format('d M'),
            'count' => QuizSession::where('status', 'completed')
                ->whereDate('completed_at', $date)->count(),
        ];
    });

    return view('admin.dashboard', compact(
        'stats', 'planBreakdown', 'questionsBySubject', 'sessionsChart'
    ));
}

public function revenue()
{
    // Placeholder until Paystack subscriptions table is built
    $subscriptions  = collect([]);
    $monthlyRevenue = collect(range(5, 0))->map(fn($i) => [
        'month'   => now()->subMonths($i)->format('M Y'),
        'revenue' => 0,
        'subs'    => 0,
    ]);
    $planStats = ['monthly' => 0, 'quarterly' => 0, 'yearly' => 0];

    return view('admin.revenue', compact('subscriptions', 'monthlyRevenue', 'planStats'));
}

    // ============================================================
    // USERS
    // ============================================================
    public function users(Request $request)
    {
        $query = User::query()->where('is_admin', false);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->filter === 'premium') {
            $query->where('subscription_status', 'active');
        } elseif ($request->filter === 'free') {
            $query->where(function ($q) {
                $q->where('subscription_status', '!=', 'active')->orWhereNull('subscription_status');
            });
        } elseif ($request->filter === 'banned') {
            $query->where('is_banned', true);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function makeUserPremium(User $user)
    {
        $user->update([
            'subscription_status'     => 'active',
            'subscription_expires_at' => now()->addMonth(),
        ]);

        return back()->with('success', "{$user->name} upgraded to Premium for 1 month.");
    }

    public function banUser(User $user)
    {
        $user->update(['is_banned' => !$user->is_banned]);
        $action = $user->is_banned ? 'banned' : 'unbanned';
        return back()->with('success', "{$user->name} has been {$action}.");
    }

    // ============================================================
    // DISCOUNT CODES
    // ============================================================
    public function discountCodes()
    {
        $codes = DiscountCode::latest()->get();
        return view('admin.discount-codes', compact('codes'));
    }

    public function createDiscountCode(Request $request)
    {
        $request->validate([
            'code'        => 'required|unique:discount_codes,code|max:20',
            'percent'     => 'required|integer|min:1|max:100',
            'max_uses'    => 'nullable|integer|min:1',
            'expires_at'  => 'nullable|date|after:today',
            'description' => 'nullable|string|max:255',
        ]);

        DiscountCode::create([
            'code'        => strtoupper($request->code),
            'percent'     => $request->percent,
            'max_uses'    => $request->max_uses,
            'expires_at'  => $request->expires_at,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        return back()->with('success', "Discount code {$request->code} created.");
    }

    public function toggleDiscountCode(DiscountCode $code)
    {
        $code->update(['is_active' => !$code->is_active]);
        $status = $code->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Code {$code->code} {$status}.");
    }

    public function deleteDiscountCode(DiscountCode $code)
    {
        $code->delete();
        return back()->with('success', 'Discount code deleted.');
    }

    public function generateDiscountCode()
    {
        return response()->json([
            'code' => 'EP' . strtoupper(Str::random(6))
        ]);
    }

    // ============================================================
    // REVENUE
    // ============================================================
  

    // ============================================================
    // MARKETERS
    // ============================================================
    public function marketers()
    {
        $marketers = Marketer::latest()->get();
        return view('admin.marketers', compact('marketers'));
    }

    public function createMarketer(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email|unique:marketers,email',
            'phone' => 'nullable|string',
        ]);

        $code = 'MK' . strtoupper(Str::random(6));

        Marketer::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'referral_code' => $code,
        ]);

        return back()->with('success', "Marketer created. Referral code: {$code}");
    }

    public function toggleMarketer(Marketer $marketer)
    {
        $marketer->update(['is_active' => !$marketer->is_active]);
        return back()->with('success', "Marketer {$marketer->name} status updated.");
    }

    public function payMarketer(Marketer $marketer)
    {
        $pending = $marketer->pending_commission;

        if ($pending <= 0) {
            return back()->with('error', 'No pending commission to pay.');
        }

        $marketer->update([
            'paid_commission'    => $marketer->paid_commission + $pending,
            'pending_commission' => 0,
        ]);

        return back()->with('success', "₦" . number_format($pending) . " marked as paid for {$marketer->name}.");
    }

    public function loginPage()
{
    if (session('is_admin')) return redirect()->route('admin.dashboard');
    return view('admin.login');
}

public function loginSubmit(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    $user = \App\Models\User::where('email', $request->email)
        ->where('is_admin', true)
        ->first();

    if (!$user || !\Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'Invalid admin credentials.']);
    }

    if ($user->is_banned) {
        return back()->withErrors(['email' => 'Account is banned.']);
    }

    session(['is_admin' => true, 'admin_name' => $user->name]);
    return redirect()->route('admin.dashboard');
}

public function logout()
{
    session()->forget(['is_admin', 'admin_name']);
    return redirect()->route('admin.login');
}

// ============================================================
// QUESTIONS MANAGEMENT
// ============================================================
public function questions()
{
    $breakdown = \App\Models\Subject::where('is_active', true)
        ->withCount([
            'questions as jamb_count' => fn($q) => $q->where('exam_type', 'JAMB'),
            'questions as waec_count' => fn($q) => $q->where('exam_type', 'WAEC'),
            'questions as total_count',
        ])
        ->orderBy('name')
        ->get();

    return view('admin.questions', compact('breakdown'));
}

public function deleteSubjectQuestions(Request $request)
{
    $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'exam_type'  => 'required|in:JAMB,WAEC,ALL',
    ]);

    $query = \App\Models\Question::where('subject_id', $request->subject_id);

    if ($request->exam_type !== 'ALL') {
        $query->where('exam_type', $request->exam_type);
    }

    $count = $query->count();
    $query->delete();

    return back()->with('success', "Deleted {$count} questions successfully.");
}

public function importCsv(Request $request)
{
    $request->validate([
        'csv_file'   => 'required|file|mimes:csv,txt|max:20480',
        'subject_id' => 'required|exists:subjects,id',
        'exam_type'  => 'required|in:JAMB,WAEC',
    ]);

    $file      = $request->file('csv_file');
    $subjectId = $request->subject_id;
    $examType  = $request->exam_type;

    $handle = fopen($file->getPathname(), 'r');
    if (!$handle) {
        return back()->with('error', 'Could not read file.');
    }

    $saved   = 0;
    $skipped = 0;
    $failed  = 0;

    // Skip header row
    fgetcsv($handle);

    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 6) { $failed++; continue; }

        // Clean all fields
        $questionRaw   = $row[0] ?? '';
        $questionText  = $this->cleanQuestionText($questionRaw);
        $optionA       = $this->cleanOption($row[1] ?? '');
        $optionB       = $this->cleanOption($row[2] ?? '');
        $optionC       = $this->cleanOption($row[3] ?? '');
        $optionD       = $this->cleanOption($row[4] ?? '');
        $correctAnswer = $this->normalizeAnswer($row[5] ?? '');
        $year          = $this->extractYear($questionRaw);

        // Skip if question or options are empty
        if (empty($questionText) || empty($optionA) || !$correctAnswer) {
            $failed++;
            continue;
        }

        // Skip very short questions — likely noise
        if (strlen($questionText) < 10) {
            $failed++;
            continue;
        }

        // Duplicate check
        $exists = \Illuminate\Support\Facades\DB::table('questions')
            ->where('subject_id', $subjectId)
            ->where('question_text', $questionText)
            ->where('exam_type', $examType)
            ->exists();

        if ($exists) { $skipped++; continue; }

        try {
            \Illuminate\Support\Facades\DB::table('questions')->insert([
                'subject_id'     => $subjectId,
                'topic_id'       => null,
                'exam_type'      => $examType,
                'year'           => $year,
                'question_text'  => $questionText,
                'option_a'       => $optionA,
                'option_b'       => $optionB,
                'option_c'       => $optionC,
                'option_d'       => $optionD,
                'correct_answer' => $correctAnswer,
                'explanation'    => null,
                'difficulty'     => 'medium',
                'image_url'      => null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            $saved++;
        } catch (\Exception $e) {
            $failed++;
        }
    }

    fclose($handle);

    return back()->with('success',
        "Import complete — Saved: {$saved} | Skipped (duplicates): {$skipped} | Failed: {$failed}"
    );
}

private function cleanQuestionText(string $raw): string
{
    // Decode HTML entities first
    $text = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Remove ONLY the myschool badge links at the top
    // These are <a href="..."><div class="...badge...">Subject Name</div></a>
    $text = preg_replace('/<a\s[^>]*>.*?<\/a>/is', '', $text);

    // Remove standalone <br> tags left after removing badges
    $text = preg_replace('/^(\s*<br\s*\/?>\s*)+/i', '', $text);

    // Remove leading/trailing whitespace but keep inner HTML intact
    $text = trim($text);

    return $text;
}

private function cleanOption(string $raw): string
{
    // Decode HTML entities
    $text = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Remove ONLY the bold letter wrapper e.g. <strong>A.</strong>
    $text = preg_replace('/<strong>\s*[A-Da-d][\.\)]\s*<\/strong>/i', '', $text);

    // Remove leading plain letter like "A." "B." in case no strong tag
    $text = preg_replace('/^\s*[A-Da-d][\.\)]\s*/u', '', trim($text));

    // Keep all other HTML — sub, sup, em, strong for formulas
    // Only remove truly useless tags — spans with no meaning
    $text = preg_replace('/<span[^>]*>(.*?)<\/span>/is', '$1', $text);

    // Collapse whitespace but preserve HTML tags
    $text = preg_replace('/\s+/', ' ', $text);

    return trim($text);
}

private function normalizeAnswer(string $raw): ?string
{
    $raw = trim($raw);

    // "Correct Answer: Option C" — myschool format
    if (preg_match('/Option\s+([A-Da-d])/i', $raw, $m)) {
        return strtoupper($m[1]);
    }

    // "option_a", "option_b" etc
    if (preg_match('/option_([a-d])/i', $raw, $m)) {
        return strtoupper($m[1]);
    }

    // Plain "A", "B", "C", "D"
    if (preg_match('/^[A-Da-d]$/', trim($raw))) {
        return strtoupper(trim($raw));
    }

    // Last resort — find any single letter
    if (preg_match('/\b([A-Da-d])\b/i', $raw, $m)) {
        return strtoupper($m[1]);
    }

    return null;
}

private function extractYear(string $raw): ?int
{
    // Extract from URL param: exam_year=2000
    if (preg_match('/exam_year=(\d{4})/', $raw, $m)) {
        return (int) $m[1];
    }

    // Extract from text like "JAMB 2019"
    if (preg_match('/\b(19|20)\d{2}\b/', $raw, $m)) {
        return (int) $m[0];
    }

    return null;
}


}