<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemActivity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'action',
        'description',
        'color'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity
     */
    public static function log($type, $action, $description, $color = 'slate')
    {
        return self::create([
            'user_id' => auth()->id(),
            'type' => $type,
            'action' => $action,
            'description' => $description,
            'color' => $color
        ]);
    }
}
