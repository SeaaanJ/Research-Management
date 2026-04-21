<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'institution',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function isUser()
    {
        return $this->role === 'user';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function groups()
{
   return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id')
                ->withTimestamps();
}
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->last_name}");
    }


    public function receivedInvites()
    {
        return $this->hasMany(GroupInvite::class, 'receiver_id');
    }
    public function sentInvites()
    {
        return $this->hasMany(GroupInvite::class, 'sender_id');
    }

    public function pendingInvites()
    {
        return $this->hasMany(GroupInvite::class, 'receiver_id')->where('status', 'pending')->with(['group', 'sender']);

    }


    public function bans()
{
    return $this->hasMany(UserBan::class);
}

public function activeBan()
{
    return $this->hasOne(UserBan::class)
        ->where('is_active', true)
        ->where(function($q) {
            $q->where('type', 'permanent')
              ->orWhere('banned_until', '>', now());
        })
        ->latest();
}

public function isBanned()
{
    $ban = $this->activeBan;
    return $ban && $ban->isActive();
}

    
}
