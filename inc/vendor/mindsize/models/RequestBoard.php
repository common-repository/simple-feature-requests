<?php
namespace SFR\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RequestBoard extends WpModel {
    protected $table = 'request_boards';
    /**
     * Columns that can be edited - IE not primary key or timestamps if being used
     */
    protected $fillable = [
        'request_id',
        'board_id'
    ];

    public function request(): HasOne {
        return $this->hasOne(Request::class);
    }

    public function board(): HasOne {
        return $this->hasOne(Board::class);
    }
}