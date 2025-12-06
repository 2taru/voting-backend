<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\VotesLog;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    // GET /api/elections/{election_id}/vote-status
    public function status(Request $request, $electionId)
    {
        $user = $request->user();
        $election = Election::find($electionId);

        if (!$election) {
            return response()->json(['message' => 'Вибори не знайдено'], 404);
        }

        if ($election->status !== 'active') {
            return response()->json([
                'status' => 'ok',
                'can_vote' => false,
                'message' => 'Вибори зараз не активні'
            ]);
        }

        $hasVoted = VotesLog::where('election_id', $electionId)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasVoted) {
            return response()->json([
                'status' => 'ok',
                'can_vote' => false,
                'message' => 'Ви вже голосували в цих виборах'
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'can_vote' => true,
            'message' => 'Ви можете голосувати'
        ]);
    }

    // POST /api/elections/{election_id}/vote
    public function store(Request $request, $electionId)
    {
        $user = $request->user();

        if (VotesLog::where('election_id', $electionId)->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Ви вже голосували'], 409);
        }

        $request->validate([
            'transaction_hash' => 'required|string|unique:votes_log,transaction_hash',
        ]);

        VotesLog::create([
            'election_id' => $electionId,
            'user_id' => $user->id,
            'transaction_hash' => $request->transaction_hash,
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Голос успішно зараховано'
        ], 201);
    }

    // GET /api/my-votes
    public function history(Request $request)
    {
        $logs = $request->user()
            ->votesLogs()
            ->with('election')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($logs);
    }
}