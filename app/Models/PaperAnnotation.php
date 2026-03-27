<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaperAnnotation extends Model
{
    protected $table = 'paper_annotations';

    protected $fillable = [
        'paper_id',
        'user_id',
        'x',
        'y',
        'comment',
    ];

    public function paper()
    {
        return $this->belongsTo(ResearchPaper::class, 'paper_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}