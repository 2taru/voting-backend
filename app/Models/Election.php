<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Election extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'on_chain_election_id',
    ];

    // Автоматичне конвертування дат у Carbon об'єкти
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Зв'язок: Вибори мають багато кандидатів
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    // Зв'язок: Лог голосів за ці вибори
    public function votesLogs(): HasMany
    {
        return $this->hasMany(VotesLog::class);
    }
}