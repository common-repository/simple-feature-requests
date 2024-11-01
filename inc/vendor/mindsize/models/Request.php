<?php
namespace SFR\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Request extends WpModel {
    /**
     * Columns that can be edited - IE not primary key or timestamps if being used
     */
    protected $fillable = [
        'board_id',
        'user_id',
        'name',
        'description',
        'status',
    ];

    public function board(): BelongsTo {
        return $this->belongsTo(Board::class);
    }
    
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany {
        return $this->hasMany(RequestComment::class);
    }

    public function votes(): HasMany {
        return $this->hasMany(Vote::class);
    }
}