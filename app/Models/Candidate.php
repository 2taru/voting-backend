<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    protected $fillable = [
        'election_id',
        'user_id',
        'bio',
    ];

    // Зв'язок: Кандидат належить до одних виборів
    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    // Зв'язок: Кандидат є конкретним користувачем
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}