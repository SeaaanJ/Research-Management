<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description', 'user_id'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
                    ->withTimestamps();
    }

    public function members()
    {
        return $this->users();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ New
    public function papers()
    {
        return $this->hasMany(ResearchPaper::class);
    }
}