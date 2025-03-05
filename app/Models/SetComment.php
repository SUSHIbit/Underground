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
        'user_id',
        'comment',
        'is_resolved'
    ];

    /**
     * Get the set that owns the comment.
     */
    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    /**
     * Get the question that this comment is about (if applicable).
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the user who made the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
