<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserBan extends Model
{
    protected $fillable = [
        'user_id',
        'banned_by',
        'type',
        'banned_until',
        'reason',
        'is_active',
    ];

    protected $casts = [
        'banned_until' => 'datetime',
        'is_active'    => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function banner()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    // Check if ban is still active
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->type === 'permanent') {
            return true;
        }

        // Temporary ban
        if ($this->banned_until && Carbon::now()->greaterThan($this->banned_until)) {
            // Ban expired, deactivate it
            $this->update(['is_active' => false]);
            return false;
        }

        return true;
    }

    // Get remaining time
    public function getRemainingTime()
    {
        if ($this->type === 'permanent') {
            return 'Permanent';
        }

        if (!$this->banned_until) {
            return 'N/A';
        }

        return Carbon::now()->diffForHumans($this->banned_until, true);
    }
}