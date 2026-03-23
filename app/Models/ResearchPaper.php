<?php
// app/Models/ResearchPaper.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResearchPaper extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}