<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    // GET /api/elections/{election_id}/candidates
    public function index($electionId)
    {
        $election = Election::find($electionId);
        if (!$election) {
            return response()->json(['message' => 'Вибори не знайдено'], 404);
        }
        $candidates = $election->candidates()->with('user')->get();

        return response()->json($candidates);
    }

    // POST /api/elections/{election_id}/candidates
    public function store(Request $request, $electionId)
    {
        $election = Election::find($electionId);
        if (!$election) {
            return response()->json(['message' => 'Вибори не знайдено'], 404);
        }

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('candidates')->where(function ($query) use ($electionId) {
                    return $query->where('election_id', $electionId);
                }),
            ],
            'bio' => 'nullable|string',
        ], [
            'user_id.unique' => 'Цей користувач уже є кандидатом на цих виборах.'
        ]);

        $candidate = Candidate::create([
            'election_id' => $electionId,
            'user_id' => $request->user_id,
            'bio' => $request->bio,
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Кандидата додано успішно!',
            'candidate' => $candidate
        ], 201);
    }

    // GET /api/candidates/{id}
    public function show($id)
    {
        $candidate = Candidate::with(['user', 'election'])->find($id);

        if (!$candidate) {
            return response()->json(['message' => 'Кандидата не знайдено'], 404);
        }

        return response()->json($candidate);
    }

    // PUT /api/candidates/{id}
    public function update(Request $request, $id)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return response()->json(['message' => 'Кандидата не знайдено'], 404);
        }

        $validated = $request->validate([
            'bio' => 'sometimes|required|string',
        ]);

        $candidate->update($validated);

        return response()->json([
            'status' => 'ok',
            'message' => 'Дані успішно оновлено',
            'candidate' => $candidate
        ]);
    }

    // DELETE /api/candidates/{id}
    public function destroy($id)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return response()->json(['message' => 'Кандидата не знайдено'], 404);
        }

        $candidate->delete();

        return response()->json([
            'status' => 'ok',
            'message' => 'Успішно видалено кандидата'
        ]);
    }
}