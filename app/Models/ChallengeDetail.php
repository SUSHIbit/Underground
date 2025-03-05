<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeDetail extends Model
{
    use HasFactory;

    protected $fillable = ['set_id', 'name'];

    public function set()
    {
        return $this->belongsTo(Set::class);
    }

    public function prerequisites()
    {
        return $this->belongsToMany(
            Set::class,
            'challenge_prerequisites',
            'challenge_id',
            'prerequisite_set_id'
        );
    }

    public function hasCompletedPrerequisites(User $user)
    {
        $prerequisiteIds = $this->prerequisites()->pluck('sets.id');
        $completedIds = $user->completedSets()->pluck('sets.id');
        
        return $prerequisiteIds->diff($completedIds)->isEmpty();
    }
}
