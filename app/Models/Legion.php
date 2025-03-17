<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Legion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'emblem',
        'description',
        'leader_id',
    ];

    /**
     * Get the leader of the legion.
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get the members of the legion.
     */
    public function members()
    {
        return $this->hasMany(LegionMember::class);
    }

    /**
     * Get all users in the legion.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'legion_members')
                    ->withPivot('role', 'is_accepted', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Get accepted members of the legion.
     */
    public function acceptedMembers()
    {
        return $this->hasMany(LegionMember::class)->where('is_accepted', true);
    }

    /**
     * Get pending members of the legion.
     */
    public function pendingMembers()
    {
        return $this->hasMany(LegionMember::class)->where('is_accepted', false);
    }

    /**
     * Get officers of the legion.
     */
    public function officers()
    {
        return $this->hasMany(LegionMember::class)->where('role', 'officer');
    }

    /**
     * Calculate the legion's power (average points of all members).
     */
    public function getPowerAttribute()
    {
        $acceptedMembers = $this->acceptedMembers()->with('user')->get();
        
        if ($acceptedMembers->isEmpty()) {
            return 0;
        }
        
        $totalPoints = $acceptedMembers->sum(function ($member) {
            return $member->user->points;
        });
        
        return round($totalPoints / $acceptedMembers->count(), 2);
    }

    /**
     * Get the total points of all members combined.
     */
    public function getTotalPointsAttribute()
    {
        return $this->acceptedMembers()->with('user')->get()->sum(function ($member) {
            return $member->user->points;
        });
    }

    /**
     * Get the member count for the legion.
     */
    public function getMemberCountAttribute()
    {
        return $this->acceptedMembers()->count();
    }

    /**
     * Check if the legion is full (10 members).
     */
    public function isLegionFull()
    {
        return $this->member_count >= 10;
    }
}