<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    // Отримати список всіх виборів
    public function index()
    {
        $elections = Election::orderBy('created_at', 'desc')->get();
        return response()->json($elections);
    }

    // Створити нові вибори (Тільки для Адміна!)
    public function store(Request $request)
    {
        // У реальному додатку тут варто додати перевірку: if ($request->user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $election = Election::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'planned', // За замовчуванням планується
        ]);

        return response()->json([
            'message' => 'Election created successfully',
            'election' => $election
        ], 201);
    }

    // Показати конкретні вибори
    public function show($id)
    {
        $election = Election::find($id);

        if (!$election) {
            return response()->json(['message' => 'Election not found'], 404);
        }

        return response()->json($election);
    }

    // --- НОВИЙ МЕТОД: ОНОВЛЕННЯ ВИБОРІВ ---
    public function update(Request $request, $id)
    {
        // Тут також потрібна перевірка на адміна
        // if ($request->user()->role !== 'admin') abort(403);

        $election = Election::find($id);
        if (!$election) {
            return response()->json(['message' => 'Election not found'], 404);
        }

        // 'sometimes|required' означає: якщо поле є, воно має бути валідним, але воно не обов'язкове
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|in:planned,active,completed' // Додаємо оновлення статусу
        ]);

        $election->update($validated);

        return response()->json([
            'message' => 'Election updated successfully',
            'election' => $election
        ]);
    }

    // --- НОВИЙ МЕТОД: ВИДАЛЕННЯ ВИБОРІВ ---
    public function destroy(Request $request, $id)
    {
        // Тут також потрібна перевірка на адміна
        // if ($request->user()->role !== 'admin') abort(403);

        $election = Election::find($id);
        if (!$election) {
            return response()->json(['message' => 'Election not found'], 404);
        }

        // onDelete('cascade') у міграції подбає про пов'язаних кандидатів та логи
        $election->delete();

        return response()->json([
            'message' => 'Election deleted successfully'
        ], 200);
    }
}