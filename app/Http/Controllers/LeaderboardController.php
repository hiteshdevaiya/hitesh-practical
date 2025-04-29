<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LeaderboardService;

class LeaderboardController extends Controller
{
    protected LeaderboardService $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    /**
     * Display the leaderboard with optional filters and search.
     */
    public function leaderboard(Request $request)
    {
        $filter = $request->input('filter');
        $search = $request->input('search');

        $users = $this->leaderboardService->getLeaderboard($filter, $search);

        return response()->json($users);
    }

    /**
     * Handle recalculation of leaderboard.
     * Recalculate and return the updated leaderboard data as JSON.
     */
    public function recalculate()
    {
        $this->leaderboardService->recalculateLeaderboard();

        $users = $this->leaderboardService->getLeaderboard();

        return response()->json([
            'message' => 'Leaderboard recalculated successfully!',
            'users' => $users
        ]);
    }
}
