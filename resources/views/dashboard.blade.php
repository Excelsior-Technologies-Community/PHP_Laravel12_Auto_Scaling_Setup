<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Scaling Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-slate-100">

    <div class="max-w-7xl mx-auto py-8 px-4">

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">

            <div>
                <h1 class="text-4xl font-bold text-slate-800">
                    🚀 Laravel Auto Scaling Dashboard
                </h1>

                <p class="text-slate-500 mt-2">
                    Monitor Auto Scaling Metrics & Worker Performance
                </p>
            </div>

            <div class="mt-4 md:mt-0">
                <a href="{{ route('export.csv') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-lg shadow font-semibold">

                    Export CSV

                </a>
            </div>

        </div>

        <!-- Success Message -->

        @if(session('success'))

        <div class="mb-6 bg-green-100 border border-green-300 text-green-700 rounded-lg p-4">

            {{ session('success') }}

        </div>

        @endif

        <!-- Error -->

        @if(session('error'))

        <div class="mb-6 bg-red-100 border border-red-300 text-red-700 rounded-lg p-4">

            {{ session('error') }}

        </div>

        @endif

        <!-- Statistics -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500 text-sm">

                    Total Logs

                </p>

                <h2 class="text-3xl font-bold text-blue-600 mt-2">

                    {{ $statistics['total_logs'] }}

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500 text-sm">

                    Scale Up

                </p>

                <h2 class="text-3xl font-bold text-green-600 mt-2">

                    {{ $statistics['scale_up'] }}

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500 text-sm">

                    Scale Down

                </p>

                <h2 class="text-3xl font-bold text-red-600 mt-2">

                    {{ $statistics['scale_down'] }}

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500 text-sm">

                    Maintain

                </p>

                <h2 class="text-3xl font-bold text-indigo-600 mt-2">

                    {{ $statistics['maintain'] }}

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow p-6">

                <p class="text-gray-500 text-sm">

                    Average Load

                </p>

                <h2 class="text-3xl font-bold text-orange-600 mt-2">

                    {{ $statistics['average_load'] }}%

                </h2>

            </div>

        </div>

        <!-- Current Metrics -->

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Current Load

                </p>

                <h2 class="text-4xl font-bold mt-3

            {{ $metrics['load'] > 70 ? 'text-red-600' : ($metrics['load'] < 30 ? 'text-green-600' : 'text-blue-600') }}">

                    {{ $metrics['load'] }}%

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Active Workers

                </p>

                <h2 class="text-4xl font-bold text-indigo-600 mt-3">

                    {{ $metrics['workers'] }}

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Requests / Minute

                </p>

                <h2 class="text-4xl font-bold text-purple-600 mt-3">

                    {{ $metrics['requests_per_minute'] }}

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Memory Usage

                </p>

                <h2 class="text-4xl font-bold text-yellow-600 mt-3">

                    {{ number_format($metrics['memory_usage'],2) }} MB

                </h2>

            </div>

        </div>


        <!-- Load Simulation -->

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">

            <h2 class="text-2xl font-bold text-slate-800 mb-6">
                ⚡ Load Simulation
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <!-- Random -->

                <form method="GET"
                    action="{{ route('simulate.random') }}">

                    <button
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-semibold">

                        🎲 Random Load

                    </button>

                </form>

                <!-- Pattern -->

                <form method="GET"
                    action="{{ route('simulate.pattern') }}">

                    <button
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg font-semibold">

                        📈 Pattern Load

                    </button>

                </form>

                <!-- Custom -->

                <form method="POST"
                    action="{{ route('simulate.custom') }}">

                    @csrf

                    <div class="flex">

                        <input
                            type="number"
                            name="load"
                            min="1"
                            max="100"
                            required
                            placeholder="Enter Load %"
                            class="w-full border rounded-l-lg px-4 py-3">

                        <button
                            class="bg-green-600 hover:bg-green-700 text-white px-5 rounded-r-lg">

                            Set

                        </button>

                    </div>

                </form>

                <!-- Reset -->

                <form method="POST"
                    action="{{ route('reset') }}">

                    @csrf

                    <button
                        class="w-full bg-gray-700 hover:bg-black text-white py-3 rounded-lg font-semibold">

                        🔄 Reset

                    </button>

                </form>

            </div>

        </div>


        <!-- Search Filter -->

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">

            <form method="GET"
                action="{{ route('dashboard') }}">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search..."
                        class="border rounded-lg px-4 py-3">

                    <select
                        name="action"
                        class="border rounded-lg px-4 py-3">

                        <option value="">All Actions</option>

                        <option value="scale_up"

                            {{ request('action')=='scale_up' ? 'selected' : '' }}>

                            Scale Up

                        </option>

                        <option value="scale_down"

                            {{ request('action')=='scale_down' ? 'selected' : '' }}>

                            Scale Down

                        </option>

                        <option value="maintain"

                            {{ request('action')=='maintain' ? 'selected' : '' }}>

                            Maintain

                        </option>

                    </select>

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="border rounded-lg px-4 py-3">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold px-4 py-3">
                        🔍 Search
                    </button>

                    <a href="{{ route('dashboard') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white rounded-lg flex items-center justify-center font-semibold">

                        Reset

                    </a>

                </div>

            </form>

        </div>


        <!-- Scaling History -->

        <div class="bg-white rounded-xl shadow-lg">

            <div class="flex justify-between items-center p-6 border-b">

                <h2 class="text-2xl font-bold">

                    📋 Scaling History

                </h2>

                <form method="POST"
                    action="{{ route('history.deleteAll') }}"
                    onsubmit="return confirm('Delete all history?')">

                    @csrf
                    @method('DELETE')

                    <button
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg">

                        Delete All

                    </button>

                </form>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-100">

                        <tr>

                            <th class="px-5 py-3 text-left">Date</th>

                            <th class="px-5 py-3 text-left">Action</th>

                            <th class="px-5 py-3 text-left">Workers</th>

                            <th class="px-5 py-3 text-left">Load</th>

                            <th class="px-5 py-3 text-left">Reason</th>

                            <th class="px-5 py-3 text-center">Delete</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($history as $log)

                        <tr class="border-b hover:bg-slate-50">

                            <td class="px-5 py-4">

                                {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y H:i') }}

                            </td>

                            <td class="px-5 py-4">

                                @if($log->action=='scale_up')

                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">

                                    ▲ Scale Up

                                </span>

                                @elseif($log->action=='scale_down')

                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">

                                    ▼ Scale Down

                                </span>

                                @else

                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">

                                    ● Maintain

                                </span>

                                @endif

                            </td>

                            <td class="px-5 py-4 font-semibold">

                                {{ $log->current_workers }}

                                →

                                {{ $log->new_workers }}

                            </td>

                            <td class="px-5 py-4">

                                <span class="font-bold">

                                    {{ $log->load_percentage }}%

                                </span>

                            </td>

                            <td class="px-5 py-4 text-gray-600">

                                {{ $log->reason }}

                            </td>

                            <td class="px-5 py-4 text-center">

                                <form
                                    method="POST"
                                    action="{{ route('history.delete',$log->id) }}"
                                    onsubmit="return confirm('Delete this record?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">

                                        🗑

                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6"
                                class="text-center py-8 text-gray-500">

                                No scaling history found.

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- Pagination -->

        <div class="mt-6">

            {{ $history->links() }}

        </div>

        <!-- Information Panel -->

        <div class="mt-10 bg-blue-50 border border-blue-200 rounded-xl p-6">

            <h2 class="text-2xl font-bold text-blue-800 mb-6">
                ⚙️ Auto Scaling Rules
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white rounded-lg shadow p-5">

                    <div class="text-4xl mb-3">
                        📈
                    </div>

                    <h3 class="font-bold text-lg mb-2">

                        Scale Up

                    </h3>

                    <p class="text-gray-600">

                        If CPU Load is greater than

                        <strong>{{ $scaleUpThreshold }}%</strong>

                        one worker will automatically be added.

                    </p>

                </div>

                <div class="bg-white rounded-lg shadow p-5">

                    <div class="text-4xl mb-3">
                        📉
                    </div>

                    <h3 class="font-bold text-lg mb-2">

                        Scale Down

                    </h3>

                    <p class="text-gray-600">

                        If CPU Load is below

                        <strong>{{ $scaleDownThreshold }}%</strong>

                        one worker will automatically be removed.

                    </p>

                </div>

                <div class="bg-white rounded-lg shadow p-5">

                    <div class="text-4xl mb-3">
                        ⏳
                    </div>

                    <h3 class="font-bold text-lg mb-2">

                        Cooldown

                    </h3>

                    <p class="text-gray-600">

                        Scaling waits 60 seconds before another scaling action
                        can occur.

                    </p>

                </div>

            </div>

        </div>

        <!-- Charts -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-10">

            <div class="bg-white rounded-xl shadow-lg p-6">

                <h2 class="text-xl font-bold mb-4">

                    Current Load

                </h2>

                <canvas id="loadGauge" height="220"></canvas>

            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">

                <h2 class="text-xl font-bold mb-4">

                    Workers Overview

                </h2>

                <canvas id="workerChart" height="220"></canvas>

            </div>

        </div>

    </div>

    <script>
        const loadCtx = document.getElementById('loadGauge').getContext('2d');

        new Chart(loadCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{
                            {
                                $metrics['load']
                            }
                        },
                        {
                            {
                                100 - $metrics['load']
                            }
                        }
                    ],
                    backgroundColor: [
                        @if($metrics['load'] > 70)
                        '#ef4444',
                        @elseif($metrics['load'] < 30)
                        '#10b981',
                        @else '#3b82f6',
                        @endif '#e5e7eb'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        const workerCtx = document.getElementById('workerChart').getContext('2d');

        new Chart(workerCtx, {
            type: 'bar',
            data: {
                labels: [
                    'Workers',
                    'Queue',
                    'Requests'
                ],
                datasets: [{
                    label: 'System Metrics',
                    data: [{
                            {
                                $metrics['workers']
                            }
                        },
                        {
                            {
                                $metrics['queue_size']
                            }
                        },
                        {
                            {
                                $metrics['requests_per_minute']
                            }
                        }
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Auto Refresh Dashboard
        |--------------------------------------------------------------------------
        */

        setInterval(function() {

            fetch("{{ route('metrics.json') }}")

                .then(response => response.json())

                .then(data => {

                    console.log("Live Metrics", data);

                });

        }, 10000);
    </script>

</body>

</html>