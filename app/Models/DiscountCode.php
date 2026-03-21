<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    protected $fillable = [
        'code', 'percent', 'max_uses', 'used_count',
        'expires_at', 'is_active', 'description'
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active'  => 'boolean',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }
}