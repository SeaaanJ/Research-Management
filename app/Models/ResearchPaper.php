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
        'abstract',
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

   public function comments()
{
    return $this->hasMany(PaperComment::class, 'research_paper_id');
}
}