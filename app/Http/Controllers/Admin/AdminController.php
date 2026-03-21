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
}