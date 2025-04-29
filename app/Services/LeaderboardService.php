<?php
namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LeaderboardService
{
    /**
     * Fetch leaderboard users with optional filters and search with user id and name.
     *
     * @param string|null $filter
     * @param int|null $searchUserId
     * @return Collection
     */
    public function getLeaderboard(?string $filter = null, ?string $search = null): Collection
    {
        $query = User::query();

        if ($filter) {
            $query->whereHas('activities', function ($q) use ($filter) {
                if ($filter === 'day') {
                    $q->whereDate('performed_at', Carbon::today());
                } elseif ($filter === 'month') {
                    $q->whereMonth('performed_at', Carbon::now()->month)
                      ->whereYear('performed_at', Carbon::now()->year);
                } elseif ($filter === 'year') {
                    $q->whereYear('performed_at', Carbon::now()->year);
                }
            });
        }

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('id', 'like', '%' . $search . '%');
        }

        return $query->orderByDesc('total_points')
                     ->orderBy('id')
                     ->get();
    }

    /**
     * Recalculate users' total points with update and ranks based on activities.
     *
     * @return void
     */
    public function recalculateLeaderboard(): void
    {
        DB::transaction(function () {
            User::query()->update(['total_points' => 0, 'rank' => null]);

            $activities = Activity::select('user_id', DB::raw('SUM(points) as total'))
                ->groupBy('user_id')
                ->get();

            foreach ($activities as $activity) {
                User::where('id', $activity->user_id)
                    ->update(['total_points' => $activity->total]);
            }

            $rank = 1;
            $prevPoints = null;
            $currentRank = 1;

            $users = User::orderByDesc('total_points')->orderBy('id')->get();

            foreach ($users as $user) {
                if ($prevPoints !== null && $user->total_points < $prevPoints) {
                    $currentRank = $rank;
                }

                $user->rank = $currentRank;
                $user->save();

                $prevPoints = $user->total_points;
                $rank++;
            }
        });
    }
}
