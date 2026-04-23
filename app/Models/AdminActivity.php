<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    protected $fillable = [
        'admin_id',
        'action_type',
        'description',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Helper to log activity
    public static function log($actionType, $description, $metadata = [])
    {
        return self::create([
            'admin_id'    => auth()->id(),
            'action_type' => $actionType,
            'description' => $description,
            'metadata'    => $metadata,
            'ip_address'  => request()->ip(),
        ]);
    }
}