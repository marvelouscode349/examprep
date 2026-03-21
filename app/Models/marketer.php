<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marketer extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'referral_code',
        'total_referrals', 'total_commission',
        'paid_commission', 'pending_commission', 'is_active'
    ];
}