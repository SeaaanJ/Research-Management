<?php
// app/Models/PaperComment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperComment extends Model
{
    protected $fillable = [
        'research_paper_id',
        'user_id',
        'comment',
    ];

    public function paper()
    {
        return $this->belongsTo(ResearchPaper::class, 'research_paper_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}