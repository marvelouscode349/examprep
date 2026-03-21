<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'stream',
        'exam_types',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'exam_types' => 'array',
            'is_active'  => 'boolean',
        ];
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}