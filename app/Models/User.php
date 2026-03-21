<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'target_exam',
        'stream',
        'exam_year',
        'state',
        'subscription_status',
        'subscription_expires_at',
        'referral_code',
        'study_plan_generated_at',
        'study_plan'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'subscription_expires_at'=> 'datetime',
            'password'               => 'hashed',
            'last_studied_at' => 'date',
            'study_plan_generated_at' => 'datetime',
        ];
    }

    // ── Relationships (add more as you build) ──────────────────

    /**
     * The marketer who referred this user at signup.
     * Links via the referral_code the user entered during registration.
     */
    // public function referredBy()
    // {
    //     return $this->belongsTo(Marketer::class, 'referral_code', 'referral_code');
    // }

    // ── Helper Methods ─────────────────────────────────────────

    /**
     * Check if user currently has an active paid subscription.
     */
    public function isPremium(): bool
    {
        return $this->subscription_status === 'active'
            && $this->subscription_expires_at
            && $this->subscription_expires_at->isFuture();
    }

    /**
     * Check if user is on the free plan.
     */
    public function isFree(): bool
    {
        return !$this->isPremium();
    }
}