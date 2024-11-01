<?php
namespace SFR\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestComment extends WpModel {
    protected $table = 'request_comments';
    /**
     * Columns that can be edited - IE not primary key or timestamps if being used
     */
    protected $fillable = [
        'request_id',
        'user_id',
        'comment',
        'created_at',
        'updated_at'
    ];

    public function request(): BelongsTo {
        return $this->belongsTo(Request::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}