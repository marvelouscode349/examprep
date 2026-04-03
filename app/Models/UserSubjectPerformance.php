<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubjectPerformance extends Model

{

// protected $table = 'user_subject_performance';
    protected $fillable = [
        'user_id',
        'subject_id',
        'topic_id',
        'total_answered',
        'total_correct',
        'accuracy',
        'last_practiced_at',
    ];
    protected function casts(): array
    {
        return [
            'last_practiced_at' => 'datetime',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
{
    return $this->belongsTo(Topic::class);
}
}