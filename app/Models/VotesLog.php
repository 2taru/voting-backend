<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VotesLog extends Model
{
    // Вказуємо ім'я таблиці явно, бо Laravel може шукати 'votes_logs'
    protected $table = 'votes_log';

    protected $fillable = [
        'election_id',
        'user_id',
        'transaction_hash',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(Election::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}