<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(20)->create()->each(function ($user) {
            for ($i = 0; $i < rand(10, 50); $i++) {
                Activity::create([
                    'user_id' => $user->id,
                    'performed_at' => Carbon::now()->subDays(rand(0, 365)),
                    'points' => 20,
                ]);
            }

            // Update user points
            $user->total_points = $user->activities()->sum('points');
            $user->save();
        });
    }
}
