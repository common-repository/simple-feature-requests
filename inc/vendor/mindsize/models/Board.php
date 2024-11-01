<?php
namespace SFR\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends WpModel {
    /**
     * Columns that can be edited - IE not primary key or timestamps if being used
     */
    protected $fillable = [
        'name',
        'description'
    ];

    public function requests(): HasMany {
        return $this->hasMany(Request::class);
    }
}