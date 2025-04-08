<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    use HasFactory;

    protected $fillable = ['set_number', 'type', 'created_by'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quizDetail()
    {
        return $this->hasOne(QuizDetail::class);
    }

    public function challengeDetail()
    {
        return $this->hasOne(ChallengeDetail::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isAttemptedBy(User $user)
    {
        return $this->attempts()->where('user_id', $user->id)
                               ->where('completed', true)
                               ->exists();
    }

    public function isQuiz()
    {
        return $this->type === 'quiz';
    }

    public function isChallenge()
    {
        return $this->type === 'challenge';
    }

    /**
     * Get the user who reviewed the set.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get all comments for this set.
     */
    public function comments()
    {
        return $this->hasMany(SetComment::class);
    }

    /**
     * Check if this set is in draft state.
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if this set is pending approval.
     */
    public function isPendingApproval()
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Check if this set is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * Check if this set is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Submit this set for approval.
     */
    public function submitForApproval()
    {
        $this->status = 'pending_approval';
        $this->submitted_at = now(); // This should return a Carbon instance
        $this->save();
        
        return $this;
    }

    /**
     * Approve this set.
     */
    public function approve(User $accessor, $notes = null)
    {
        $this->status = 'approved';
        $this->reviewed_at = now();
        $this->reviewed_by = $accessor->id;
        $this->review_notes = $notes;
        $this->save();
        
        return $this;
    }

    /**
     * Reject this set.
     */
    public function reject(User $accessor, $notes = null)
    {
        $this->status = 'rejected';
        $this->reviewed_at = now();
        $this->reviewed_by = $accessor->id;
        $this->review_notes = $notes;
        $this->save();
        
        return $this;
    }

    /**
     * Check if this set is approved but not published.
     */
    public function isApprovedUnpublished()
    {
        return $this->status === 'approved_unpublished';
    }

    /**
     * Check if this set is published.
     */
    public function isPublished()
    {
        return $this->status === 'approved';
    }

    /**
     * Publish this set.
     */
    public function publish()
    {
        if ($this->status === 'approved_unpublished') {
            $this->status = 'approved';
            $this->published_at = now();
            $this->save();
        }
        
        return $this;
    }
}
