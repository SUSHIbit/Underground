<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'set_id',
        'question_id',
        'commentable_id',
        'commentable_type',
        'user_id',
        'comment',
        'is_resolved'
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}