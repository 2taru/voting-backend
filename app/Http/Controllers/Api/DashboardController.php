<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Election;
use App\Models\User;
use App\Models\VotesLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'total_users' => User::where('role', 'voter')->count(),
            'total_elections' => Election::count(),
            'active_elections' => Election::where('status', 'active')->count(),
            'total_votes' => VotesLog::count(),
        ]);
    }
}