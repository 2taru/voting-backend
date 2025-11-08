<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CandidateController extends Controller
{
    // Отримати всіх кандидатів КОНКРЕТНИХ виборів
    // GET /api/elections/{election_id}/candidates
    public function index($electionId)
    {
        $election = Election::find($electionId);
        if (!$election) {
            return response()->json(['message' => 'Election not found'], 404);
        }

        // Завантажуємо кандидатів разом з інформацією про користувача (ім'я, email тощо)
        $candidates = $election->candidates()->with('user')->get();

        return response()->json($candidates);
    }

    // Додати кандидата до виборів
    // POST /api/elections/{election_id}/candidates
    public function store(Request $request, $electionId)
    {
        $election = Election::find($electionId);
        if (!$election) {
            return response()->json(['message' => 'Election not found'], 404);
        }

        $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                // Перевірка унікальності: користувач не може бути двічі кандидатом на ОДНИХ виборах
                Rule::unique('candidates')->where(function ($query) use ($electionId) {
                    return $query->where('election_id', $electionId);
                }),
            ],
            'bio' => 'nullable|string',
        ], [
            // Кастомне повідомлення про помилку унікальності
            'user_id.unique' => 'This user is already a candidate for this election.'
        ]);

        $candidate = Candidate::create([
            'election_id' => $electionId,
            'user_id' => $request->user_id,
            'bio' => $request->bio,
        ]);

        return response()->json([
            'message' => 'Candidate added successfully',
            'candidate' => $candidate
        ], 201);
    }

    // Отримати інформацію про конкретного кандидата
    // GET /api/candidates/{id}
    public function show($id)
    {
        // Завантажуємо разом з даними про вибори та користувача
        $candidate = Candidate::with(['user', 'election'])->find($id);

        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found'], 404);
        }

        return response()->json($candidate);
    }

    // Оновити дані кандидата (наприклад, біографію)
    // PUT /api/candidates/{id}
    public function update(Request $request, $id)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found'], 404);
        }

        $validated = $request->validate([
            'bio' => 'sometimes|required|string',
        ]);

        $candidate->update($validated);

        return response()->json([
            'message' => 'Candidate updated successfully',
            'candidate' => $candidate
        ]);
    }

    // Видалити кандидата
    // DELETE /api/candidates/{id}
    public function destroy($id)
    {
        $candidate = Candidate::find($id);
        if (!$candidate) {
            return response()->json(['message' => 'Candidate not found'], 404);
        }

        $candidate->delete();

        return response()->json(['message' => 'Candidate removed successfully']);
    }
}