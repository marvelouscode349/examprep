<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiscountCode;
use App\Models\Marketer;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaystackController extends Controller
{
    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
    }

    private array $prices = [
        'weekly'   => 40000,    // ₦400
        'monthly'   => 149900,   // ₦1,499
        'yearly'    => 1499900,  // ₦14,999
    ];

    private array $naira = [
        'weekly'   => 400,
        'monthly'   => 1499,
        'yearly'    => 14999,
    ];

    private array $planLabels = [
    'weekly'  => 'Weekly Plan',
    'monthly' => 'Monthly Plan',
    'yearly'  => 'Yearly Plan',
];

    // ============================================================
    // VALIDATE DISCOUNT CODE
    // POST /api/subscription/validate-code
    // ============================================================
  public function validateCode(Request $request)
{
    $request->validate([
        'code' => 'required|string',
        'plan' => 'required|in:weekly,monthly,yearly',
    ]);

    // 🚫 Block discount for weekly plan
    if ($request->plan === 'weekly') {
        return response()->json([
            'success' => false,
            'message' => 'Discount codes are only valid for monthly and yearly plans.',
        ], 422);
    }

    $code = DiscountCode::where('code', strtoupper($request->code))->first();

    if (!$code || !$code->isValid()) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired discount code.',
        ], 422);
    }

    $originalPrice  = $this->naira[$request->plan];
    $discountAmount = round($originalPrice * $code->percent / 100);
    $finalPrice     = $originalPrice - $discountAmount;

    return response()->json([
        'success'         => true,
        'code'            => $code->code,
        'percent'         => $code->percent,
        'original_price'  => $originalPrice,
        'discount_amount' => $discountAmount,
        'final_price'     => $finalPrice,
        'message'         => "🎉 {$code->percent}% discount applied! You save ₦" . number_format($discountAmount),
    ]);
}

    // ============================================================
    // INITIALIZE PAYMENT
    // POST /api/subscription/initialize
    // ============================================================
    public function initialize(Request $request)
    {
        $request->validate([
        'plan' => 'required|in:weekly,monthly,yearly',   
        'discount_code' => 'nullable|string',
        ]);

        $user      = $request->user();
        $plan      = $request->plan;
        $reference = 'EP_' . strtoupper(Str::random(12)) . '_' . time();

        // Calculate price
        $baseKobo        = $this->prices[$plan];
        $baseNaira       = $this->naira[$plan];
        $discountPercent = 0;
        $discountCode    = null;
        $finalKobo       = $baseKobo;
        $finalNaira      = $baseNaira;

        if ($request->discount_code) {
            $code = DiscountCode::where('code', strtoupper($request->discount_code))->first();
            if ($code && $code->isValid()) {
                $discountPercent = $code->percent;
                $discountCode    = $code->code;
                $finalKobo       = (int) round($baseKobo * (1 - $discountPercent / 100));
                $finalNaira      = (int) round($baseNaira * (1 - $discountPercent / 100));
            }
        }

        // Initialize with Paystack
        $response = Http::withToken($this->secretKey)
            ->post('https://api.paystack.co/transaction/initialize', [
                'email'     => $user->email,
                'amount'    => $finalKobo,
                'reference' => $reference,
                'currency'  => 'NGN',
                'channels'  => ['card', 'bank', 'ussd', 'qr', 'mobile_money', 'bank_transfer'],
                'metadata'  => [
                    'user_id'          => $user->id,
                    'plan'             => $plan,
                    'plan_label'       => $this->planLabels[$plan],
                    'discount_code'    => $discountCode,
                    'discount_percent' => $discountPercent,
                    'original_amount'  => $baseNaira,
                    'final_amount'     => $finalNaira,
                    'custom_fields'    => [
                        ['display_name' => 'Plan',       'variable_name' => 'plan',        'value' => $this->planLabels[$plan]],
                        ['display_name' => 'Student',    'variable_name' => 'student_name','value' => $user->name],
                    ]
                ],
            ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Could not initialize payment. Try again.',
            ], 500);
        }

        return response()->json([
            'success'          => true,
            'reference'        => $reference,
            'authorization_url'=> $response->json('data.authorization_url'),
            'access_code'      => $response->json('data.access_code'),
            'plan'             => $plan,
            'plan_label'       => $this->planLabels[$plan],
            'original_price'   => $baseNaira,
            'final_price'      => $finalNaira,
            'discount_percent' => $discountPercent,
            'discount_code'    => $discountCode,
            'amount_kobo'      => $finalKobo,
        ]);
    }

    // ============================================================
    // VERIFY PAYMENT
    // POST /api/subscription/verify
    // ============================================================
    public function verify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $user      = $request->user();
        $reference = $request->reference;

        // Check not already processed
        $already = Subscription::where('paystack_reference', $reference)->first();
        if ($already) {
            return response()->json([
                'success'    => true,
                'message'    => 'Already activated!',
                'plan'       => $already->plan,
                'expires_at' => $already->expires_at->toDateString(),
            ]);
        }

        // Verify with Paystack
        $response = Http::withToken($this->secretKey)
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Contact support.',
            ], 400);
        }

        $data   = $response->json('data');
        $status = $data['status'] ?? '';

        if ($status !== 'success') {
            return response()->json([
                'success' => false,
                'message' => 'Payment was not completed. If you paid, contact support.',
            ], 400);
        }

        // Extract metadata
        $meta            = $data['metadata']  ?? [];
        $plan            = $meta['plan']             ?? 'monthly';
        $discountCode    = $meta['discount_code']    ?? null;
        $discountPercent = $meta['discount_percent'] ?? 0;
        $finalAmount     = $meta['final_amount']     ?? $this->naira[$plan];

        // Calculate expiry based on plan
        $expiresAt = match($plan) {
            'weekly' => now()->addWeek(), // weekly
            'yearly'  => now()->addYear(),
            default   => now()->addMonth(),                   // monthly
        };

        // Save subscription
        Subscription::create([
            'user_id'            => $user->id,
            'plan'               => $plan,
            'paystack_reference' => $reference,
            'status'             => 'active',
            'amount'             => $finalAmount,
            'discount_percent'   => $discountPercent,
            'discount_code'      => $discountCode,
            'referral_code'      => $user->referred_by,
            'starts_at'          => now(),
            'expires_at'         => $expiresAt,
        ]);

        // Activate user subscription
        $user->update([
            'subscription_status'     => 'active',
            'subscription_expires_at' => $expiresAt,
        ]);

        // Increment discount code usage
        if ($discountCode) {
            DiscountCode::where('code', $discountCode)->increment('used_count');
        }

        // Credit marketer commission
        if ($user->referred_by) {
            $marketer = Marketer::where('referral_code', $user->referred_by)->first();
            if ($marketer) {
                $commission = round($finalAmount * 0.20);
                $marketer->increment('total_referrals');
                $marketer->increment('total_commission', $commission);
                $marketer->increment('pending_commission', $commission);
            }
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Subscription activated!',
            'plan'       => $plan,
            'plan_label' => $this->planLabels[$plan],
            'expires_at' => $expiresAt->toDateString(),
            'user'       => [
                'subscription_status'     => 'active',
                'subscription_expires_at' => $expiresAt->toDateString(),
            ],
        ]);
    }

    // ============================================================
    // SUBSCRIPTION STATUS
    // GET /api/subscription/status
    // ============================================================
    public function status(Request $request)
    {
        $user = $request->user();
        $user->resetDailyUsageIfNeeded();

        // Auto-expire if past expiry date
        if ($user->subscription_status === 'active' &&
            $user->subscription_expires_at &&
            $user->subscription_expires_at->isPast()) {
            $user->update(['subscription_status' => 'expired']);
        }

        return response()->json([
            'success'             => true,
            'is_premium'          => $user->isPremium(),
            'subscription_status' => $user->subscription_status,
            'plan'                => $user->subscriptions()->latest()->value('plan'),
            'expires_at'          => $user->subscription_expires_at?->toDateString(),
            'questions_remaining' => $user->isPremium() ? 'unlimited' : max(0, 10 - $user->daily_questions_used),
            'ai_remaining'        => $user->isPremium() ? 'unlimited' : max(0, 3 - $user->daily_ai_used),
        ]);
    }

    // ============================================================
    // WEBHOOK — Paystack calls this for renewals / failures
    // POST /api/webhook/paystack
    // ============================================================
    public function webhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $computed  = hash_hmac('sha512', $request->getContent(), $this->secretKey);

        if ($signature !== $computed) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data  = $request->input('data');

        // Handle charge success from webhook too
        if ($event === 'charge.success') {
            $reference = $data['reference'] ?? null;
            if ($reference) {
                // Already handled by verify() — just log it
                \Log::info("Paystack webhook: charge.success for {$reference}");
            }
        }

        return response()->json(['message' => 'ok']);
    }
}