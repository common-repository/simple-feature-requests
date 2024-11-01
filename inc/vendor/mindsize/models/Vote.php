<?php
namespace SFR\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends WpModel {
    /**
     * Columns that can be edited - IE not primary key or timestamps if being used
     */
    protected $fillable = [
        'request_id',
        'user_id'
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function request(): BelongsTo {
        return $this->belongsTo(Request::class);
    }
}