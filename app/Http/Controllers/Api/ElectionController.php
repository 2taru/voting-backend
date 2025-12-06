<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Election;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    public function index()
    {
        $elections = Election::orderBy('created_at', 'desc')->get();
        return response()->json($elections);
    }

    public function store(Request $request)
    {
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
            'status' => 'planned',
        ]);

        return response()->json([
            'status' => 'ok',
            'message' => 'Вибори успішно створено',
            'election' => $election
        ], 201);
    }

    public function show($id)
    {
        $election = Election::find($id);

        if (!$election) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Вибори не знайдено'
            ], 404);
        }

        return response()->json($election);
    }

    public function update(Request $request, $id)
    {
        $election = Election::find($id);
        if (!$election) {
            return response()->json(['message' => 'Вибори не знайдено'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|in:planned,active,completed'
        ]);

        $election->update($validated);

        return response()->json([
            'status' => 'ok',
            'message' => 'Вибори успішно оновлено',
            'election' => $election
        ]);
    }

    public function destroy($id)
    {
        $election = Election::find($id);
        if (!$election) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Вибори не знайдено'
            ], 404);
        }

        $election->delete();

        return response()->json([
            'message' => 'Вибори успішно видалено'
        ], 200);
    }
}