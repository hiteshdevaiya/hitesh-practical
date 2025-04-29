<!DOCTYPE html>
<html lang="en" x-data="leaderboardApp()" x-init="fetchLeaderboard()">

<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-50 p-8">

    <div class="max-w-6xl mx-auto">
        <h1 class="text-4xl font-bold mb-8 text-center text-red-600">🏆 Daily Activity Leaderboard</h1>

        <!-- Filters and Search -->
        <div class="flex flex-wrap justify-between items-center mb-6 gap-4">

            <div class="flex items-center gap-2">
                <select x-model="filter" @change="fetchLeaderboard()" class="border p-2 rounded">
                    <option value="">All Time</option>
                    <option value="day">Today</option>
                    <option value="month">This Month</option>
                    <option value="year">This Year</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <input type="text" x-model="search" placeholder="Search User ID" class="border p-2 rounded">
                <button @click="fetchLeaderboard()"
                    class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600 transition">
                    🔍
                </button>
            </div>

            <div>
                <button @click="recalculate()"
                    class="bg-green-600 text-white p-2 rounded hover:bg-green-700 transition">
                    ♻️ Recalculate
                </button>
            </div>

        </div>

        <!-- Loading Spinner -->
        <template x-if="loading">
            <div class="text-center my-10">
                <div class="w-10 h-10 border-4 border-blue-300 border-t-transparent rounded-full animate-spin mx-auto">
                </div>
                <p class="mt-2 text-gray-500">Loading...</p>
            </div>
        </template>

        <!-- Table -->
        <div x-show="!loading" class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full table-auto">
                <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                    <tr>
                        <th class="px-6 py-3">Rank</th>
                        <th class="px-6 py-3">User ID</th>
                        <th class="px-6 py-3">Full Name</th>
                        <th class="px-6 py-3">Total Points</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="user in users" :key="user.id">
                        <tr class="border-b hover:bg-gray-50 text-center">
                            <td class="px-6 py-4 font-bold text-lg" x-text="user.rank"></td>
                            <td class="px-6 py-4" x-text="user.id"></td>
                            <td class="px-6 py-4" x-text="user.full_name"></td>
                            <td class="px-6 py-4 text-green-700 font-semibold" x-text="user.total_points"></td>
                        </tr>
                    </template>

                    <template x-if="users.length === 0 && !loading">
                        <tr>
                            <td colspan="4" class="p-6 text-gray-400 text-center">No users found.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Flash Message -->
        <div x-show="successMessage" x-text="successMessage"
            class="mt-6 p-4 bg-green-100 text-green-800 rounded text-center"></div>

    </div>

    <script>
        function leaderboardApp() {
            return {
                users: [],
                filter: '',
                search: '',
                loading: false,
                successMessage: '',

                fetchLeaderboard() {
                    this.loading = true;
                    fetch(`/leaderboard?filter=${this.filter}&search=${this.search}`)
                        .then(response => response.json())
                        .then(data => {
                            this.users = data;
                            this.loading = false;
                        });
                },

                recalculate() {
                    this.loading = true;
                    fetch('/leaderboard/recalculate', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            this.successMessage = data.message;
                            this.users = data.users; // Update the leaderboard with the new data
                            this.loading = false;
                            setTimeout(() => this.successMessage = '', 3000);
                        });
                },
            }
        }
    </script>

</body>

</html>
