<!-- resources/views/leaderboard/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.1/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="p-10">
    <h1 class="text-3xl font-bold mb-6">Leaderboard</h1>

    {{-- Filters and Search Form --}}
    <form method="GET" action="{{ route('leaderboard') }}" class="flex items-center gap-4 mb-6">
        <select name="filter" onchange="this.form.submit()" class="border p-2 rounded">
            <option value="">All Time</option>
            <option value="day" {{ request('filter') == 'day' ? 'selected' : '' }}>Today</option>
            <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>This Month</option>
            <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>This Year</option>
        </select>

        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by User ID" class="border p-2 rounded">
        <button type="submit" class="bg-blue-500 text-white p-2 rounded">Search</button>
    </form>

    {{-- Recalculate Button --}}
    <form method="POST" action="{{ route('leaderboard.recalculate') }}">
        @csrf
        <button type="submit" class="bg-green-600 text-white p-2 rounded mb-6">Recalculate Leaderboard</button>
    </form>

    {{-- Leaderboard Table --}}
    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="border px-4 py-2">Rank</th>
                <th class="border px-4 py-2">User ID</th>
                <th class="border px-4 py-2">Full Name</th>
                <th class="border px-4 py-2">Total Points</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr class="hover:bg-gray-100 text-center">
                    <td class="border px-4 py-2">{{ $user->rank }}</td>
                    <td class="border px-4 py-2">{{ $user->id }}</td>
                    <td class="border px-4 py-2">{{ $user->full_name }}</td>
                    <td class="border px-4 py-2">{{ $user->total_points }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center p-4">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mt-6 p-4 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

</body>
</html>
