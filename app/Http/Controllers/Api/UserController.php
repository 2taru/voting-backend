<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // GET /api/users/search?q=Ivan
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('role', 'voter')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('national_id', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get(['id', 'name', 'email', 'national_id']);

        return response()->json($users);
    }

    // Оновлення адреси гаманця
    public function updateWallet(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|size:42',
        ]);

        $user = $request->user();

        $exists = User::where('wallet_address', $request->wallet_address)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Цей гаманець вже підключений в іншому акаунті'], 422);
        }

        $user->wallet_address = $request->wallet_address;
        $user->save();

        return response()->json([
            'message' => 'Гаманець успішно підключено',
            'user' => $user
        ]);
    }
}
