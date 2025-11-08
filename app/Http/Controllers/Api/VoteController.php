<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\VotesLog;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    // Перевірка: чи може поточний користувач голосувати на цих виборах?
    // GET /api/elections/{election_id}/vote-status
    public function status(Request $request, $electionId)
    {
        $user = $request->user();
        $election = Election::find($electionId);

        if (!$election) {
            return response()->json(['message' => 'Election not found'], 404);
        }

        // 1. Перевірка статусу виборів
        if ($election->status !== 'active') {
            return response()->json([
                'can_vote' => false,
                'message' => 'Election is not active'
            ]);
        }

        // 2. Перевірка верифікації користувача
        // if (!$user->is_verified) {
        //     return response()->json([
        //         'can_vote' => false,
        //         'message' => 'User is not verified to vote'
        //     ]);
        // }

        // 3. Перевірка, чи вже голосував
        $hasVoted = VotesLog::where('election_id', $electionId)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasVoted) {
            return response()->json([
                'can_vote' => false,
                'message' => 'User has already voted in this election'
            ]);
        }

        return response()->json([
            'can_vote' => true,
            'message' => 'User can vote'
        ]);
    }

    // Фіксація факту голосування (викликається ПІСЛЯ успішної відправки транзакції на фронті)
    // POST /api/elections/{election_id}/vote
    public function store(Request $request, $electionId)
    {
        $user = $request->user();

        // Повторна перевірка, щоб точно не дати проголосувати двічі
        if (VotesLog::where('election_id', $electionId)->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already voted'], 409); // 409 Conflict
        }

        $request->validate([
            'transaction_hash' => 'required|string|unique:votes_log,transaction_hash',
        ]);

        VotesLog::create([
            'election_id' => $electionId,
            'user_id' => $user->id,
            'transaction_hash' => $request->transaction_hash,
        ]);

        return response()->json(['message' => 'Vote logged successfully'], 201);
    }
}