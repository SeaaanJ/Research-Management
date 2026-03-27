<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchPaper extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'title',
        'topic',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'published',
        'published_at',
    ];

    protected $casts = [
        'published'    => 'boolean',
        'published_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ✅ Add this
    public function annotations()
    {
        return $this->hasMany(PaperAnnotation::class, 'paper_id');
    }
}