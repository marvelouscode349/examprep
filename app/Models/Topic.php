<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'subject_id',
        'name',
        'order',
        'summary',
        'summary_generated_at',
        'subtopics',
        'objectives',
    ];

    protected $casts = [
        'subtopics'            => 'array',
        'objectives'           => 'array',
        'summary_generated_at' => 'datetime',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}