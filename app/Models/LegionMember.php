<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegionMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'legion_id',
        'user_id',
        'role',
        'is_accepted',
        'joined_at',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
        'joined_at' => 'datetime',
    ];

    /**
     * Get the legion that the member belongs to.
     */
    public function legion()
    {
        return $this->belongsTo(Legion::class);
    }

    /**
     * Get the user that is the member.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the member is a leader.
     */
    public function isLeader()
    {
        return $this->role === 'leader';
    }

    /**
     * Check if the member is an officer.
     */
    public function isOfficer()
    {
        return $this->role === 'officer';
    }

    /**
     * Check if the member has management privileges (leader or officer).
     */
    public function hasManagementPrivileges()
    {
        return $this->isLeader() || $this->isOfficer();
    }
}