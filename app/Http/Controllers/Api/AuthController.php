<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|max:20',
            'password'     => 'required|string|min:6',
            'target_exam'  => 'nullable|string',
            'stream'       => 'nullable|in:science,arts,commercial,general',
            'exam_year'    => 'nullable|string|max:4',
            'state'        => 'nullable|string',
            'referral_code'=> 'nullable|string',
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'] ?? null,
            'password'      => Hash::make($validated['password']),
            'target_exam'   => $validated['target_exam'] ?? null,
            'stream'        => $validated['stream'] ?? 'science',
            'exam_year'     => $validated['exam_year'] ?? '2026',
            'state'         => $validated['state'] ?? null,
            'referral_code' => $validated['referral_code'] ?? null,
        ]);


        return response()->json([
            'success' => true,
          'user' => [
    'id'                  => $user->id,
    'name'                => $user->name,
    'email'               => $user->email,
    'phone'               => $user->phone,
    'target_exam'         => $user->target_exam,
    'stream'              => $user->stream,
    'state'               => $user->state,
    'subscription_status' => $user->subscription_status,
]
        ], 201);
    }

    /**
     * Login existing user.
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        }

        // Revoke old tokens before issuing a new one
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
          'user' => [
    'id'                  => $user->id,
    'name'                => $user->name,
    'email'               => $user->email,
    'phone'               => $user->phone,
    'target_exam'         => $user->target_exam,
    'stream'              => $user->stream,
    'state'               => $user->state,
    'subscription_status' => $user->subscription_status,
]
        ]);
    }

    /**
     * Logout — revoke current token.
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.'
        ]);
    }

    /**
     * Get currently logged in user.
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user()
        ]);
    }
}