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

                <h2 id="currentLoad"
                    class="text-4xl font-bold mt-3
                {{ $metrics['load'] > 70 ? 'text-red-600' : ($metrics['load'] < 30 ? 'text-green-600' : 'text-blue-600') }}">

                    {{ $metrics['load'] }}%

                </h2>

            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Active Workers

                </p>

                <h2 id="activeWorkers"
                    class="text-4xl font-bold text-indigo-600 mt-3">

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

        <!-- ========================================================= -->
        <!-- System Health Overview -->
        <!-- ========================================================= -->

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <!-- Worker Health -->

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">
                    Worker Health
                </p>

                @php

                $healthColor = match($workerHealth){

                'Healthy' => 'text-green-600',

                'Busy' => 'text-yellow-500',

                'Overloaded' => 'text-red-600',

                default => 'text-gray-700'

                };

                @endphp

                <h2 id="workerHealth"
                    class="text-3xl font-bold mt-3 {{ $healthColor }}">

                    {{ $workerHealth }}

                </h2>
            </div>

            <!-- Worker Efficiency -->

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Worker Efficiency

                </p>

                <h2 class="text-3xl font-bold text-blue-600 mt-3">

                    <span id="workerEfficiency">

                        {{ $workerEfficiency }}

                    </span>%

                </h2>

                <div class="w-full bg-gray-200 rounded-full h-3 mt-4">

                    <div
                        class="bg-blue-600 h-3 rounded-full"
                        style="width: {{ $workerEfficiency }}%">

                    </div>

                </div>

            </div>

            <!-- Queue Utilization -->

            <div class="bg-white rounded-xl shadow-lg p-6">

                <p class="text-gray-500 text-sm">

                    Queue Utilization

                </p>

                <h2 class="text-3xl font-bold text-purple-600 mt-3">

                    <span id="queueUtilization">

                        {{ $queueUtilization }}

                    </span>%

                </h2>

                <div class="w-full bg-gray-200 rounded-full h-3 mt-4">

                    <div
                        class="bg-purple-600 h-3 rounded-full"
                        style="width: {{ $queueUtilization }}%">

                    </div>

                </div>

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

        <!-- ========================================================= -->
        <!-- Smart Recommendation -->
        <!-- ========================================================= -->

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">

            <div class="flex items-center justify-between">

                <div>

                    <h2 class="text-2xl font-bold">

                        Smart Scaling Recommendation

                    </h2>

                    <p class="text-gray-500 mt-2">

                        Real-time recommendation generated using
                        current system load, queue size and workers.

                    </p>

                </div>

                @if($recommendationType=='up')

                <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full font-semibold">

                    Scale Up

                </span>

                @elseif($recommendationType=='down')

                <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full font-semibold">

                    Scale Down

                </span>

                @else

                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-semibold">

                    Healthy

                </span>

                @endif

            </div>

            <div class="mt-6">

                <h3 id="recommendationTitle"
                    class="text-xl font-bold">

                    {{ $recommendation }}

                </h3>

                <p id="recommendationReason"
                    class="text-gray-600 mt-3">

                    {{ $recommendationReason }}

                </p>    

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

                <div class="bg-white rounded-lg shadow p-5">

                    <div class="text-4xl mb-3">

                        ❤️

                    </div>

                    <h3 class="font-bold text-lg mb-2">

                        Worker Health

                    </h3>

                    <p class="text-gray-600">

                        Current Worker Status

<strong id="workerHealth">
    {{ $workerHealth }}
</strong>

                    </p>

                </div>

            </div>

        </div>

        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 mt-10">

            <h2 class="text-2xl font-bold text-indigo-700">

                System Summary

            </h2>

            <div class="grid md:grid-cols-2 gap-6 mt-5">

                <div>

                    <p>

                        Worker Health

<strong id="workerHealthSummary">
    {{ $workerHealth }}
</strong>

                    </p>

                    <p>

                        Efficiency

<strong>
    <span id="workerEfficiencySummary">
        {{ $workerEfficiency }}
    </span>%
</strong>

                    </p>

                </div>

                <div>

                    <p>

                        Queue Utilization

<strong>
    <span id="queueUtilizationSummary">
        {{ $queueUtilization }}
    </span>%
</strong>

                    </p>

                    <p>

                        Recommendation

<strong id="recommendationSummary">
    {{ $recommendation }}
</strong>

                    </p>

                </div>

            </div>

        </div>

        <div class="text-right text-gray-500 text-sm mb-4">

    Last Updated :

    <span id="lastUpdated">

        {{ now()->format('H:i:s') }}

    </span>

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
    data: [
        {{ $metrics['load'] }},
        {{ 100 - $metrics['load'] }}
    ],
    backgroundColor: [
        @if($metrics['load'] > 70)
            '#ef4444',
        @elseif($metrics['load'] < 30)
            '#10b981',
        @else
            '#3b82f6',
        @endif
        '#e5e7eb'
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
                    'Requests',
                    'Efficiency'
                ],
                datasets: [{
                    label: 'System Metrics',
data: [
    {{ $metrics['workers'] }},
    {{ $metrics['queue_size'] }},
    {{ $metrics['requests_per_minute'] }},
    {{ $workerEfficiency }}
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

setInterval(() => {

    fetch("{{ route('metrics.json') }}")
        .then(response => response.json())
        .then(data => {

            const metrics = data.metrics;

            document.getElementById('currentLoad').innerHTML =
                metrics.load + "%";

            document.getElementById('activeWorkers').innerHTML =
                metrics.workers;

            const queueSize = document.getElementById('queueSize');
            if (queueSize) {
                queueSize.innerHTML = metrics.queue_size;
            }

            document.getElementById('workerHealth').innerHTML =
                metrics.worker_health;

            document.getElementById('workerEfficiency').innerHTML =
                metrics.worker_efficiency;

            document.getElementById('queueUtilization').innerHTML =
                metrics.queue_utilization;

            document.getElementById('recommendationTitle').innerHTML =
                metrics.recommendation;

            document.getElementById('recommendationReason').innerHTML =
                metrics.recommendation_reason;

            document.getElementById('lastUpdated').innerHTML =
                data.generated_at;

            console.log("Dashboard Updated");

        })
        .catch(error => console.log(error));

}, 10000);
    </script>

</body>

</html>