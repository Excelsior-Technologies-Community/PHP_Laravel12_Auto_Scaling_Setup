<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Scaling Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🚀 Laravel Auto Scaling Demo</h1>
            <p class="text-gray-600">Simulate load and watch the system automatically scale workers</p>
        </div>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Current Load</p>
                        <p class="text-3xl font-bold {{ $metrics['load'] > 70 ? 'text-red-600' : ($metrics['load'] < 30 ? 'text-green-600' : 'text-blue-600') }}">
                            {{ $metrics['load'] }}%
                        </p>
                    </div>
                    <div class="w-16 h-16">
                        <canvas id="loadGauge"></canvas>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Active Workers</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ $metrics['workers'] }}</p>
                        <p class="text-sm text-gray-500">Min: 1, Max: 10</p>
                    </div>
                    <div class="text-3xl">👨‍💻</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Requests/Min</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $metrics['requests_per_minute'] }}</p>
                    </div>
                    <div class="text-3xl">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Memory Usage</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ number_format($metrics['memory_usage'], 2) }} MB</p>
                    </div>
                    <div class="text-3xl">💾</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Simulate Load</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <form method="GET" action="{{ route('simulate.random') }}" class="mb-0">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        Random Load
                    </button>
                </form>
                
                <form method="GET" action="{{ route('simulate.pattern') }}" class="mb-0">
                    <button type="submit" class="w-full bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        Pattern Load
                    </button>
                </form>
                
                <form method="POST" action="{{ route('simulate.custom') }}" class="mb-0">
                    @csrf
                    <div class="flex">
                        <input type="number" name="load" min="1" max="100" 
                               class="flex-grow border border-gray-300 rounded-l-lg px-4 py-3"
                               placeholder="Enter load % (1-100)">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold px-4 rounded-r-lg">
                            Set
                        </button>
                    </div>
                </form>
                
                <form method="POST" action="{{ route('reset') }}" class="mb-0">
                    @csrf
                    <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                        Reset System
                    </button>
                </form>
            </div>
        </div>

        <!-- Scaling History -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Scaling History</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-3 px-4 text-left">Timestamp</th>
                            <th class="py-3 px-4 text-left">Action</th>
                            <th class="py-3 px-4 text-left">Workers</th>
                            <th class="py-3 px-4 text-left">Load</th>
                            <th class="py-3 px-4 text-left">Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($history as $log)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $log->created_at }}</td>
                            <td class="py-3 px-4">
                                @if($log->action === 'scale_up')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">▲ Scale Up</span>
                                @elseif($log->action === 'scale_down')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">▼ Scale Down</span>
                                @else
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">● Maintain</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono">{{ $log->current_workers }} → {{ $log->new_workers }}</td>
                            <td class="py-3 px-4">{{ $log->load_percentage }}%</td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $log->reason }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-xl font-bold mb-4 text-blue-800">⚙️ How It Works</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h4 class="font-bold mb-2">Scale Up</h4>
                    <p class="text-sm text-gray-600">When load > {{ $scaleUpThreshold }}%, increase workers by 1</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h4 class="font-bold mb-2">Scale Down</h4>
                    <p class="text-sm text-gray-600">When load < {{ $scaleDownThreshold }}%, decrease workers by 1</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h4 class="font-bold mb-2">Cooldown</h4>
                    <p class="text-sm text-gray-600">60-second cooldown between scaling actions</p>
                </div>
            </div>
            <p class="mt-4 text-sm text-blue-700">
                💡 <strong>Note:</strong> This demo simulates auto-scaling logic. In production, use AWS Auto Scaling, Kubernetes, or Docker Swarm with actual infrastructure.
            </p>
        </div>
    </div>

    <script>
        // Load Gauge Chart
        const loadCtx = document.getElementById('loadGauge').getContext('2d');
        new Chart(loadCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $metrics['load'] }}, 100 - {{ $metrics['load'] }}],
                    backgroundColor: [
                        {{ $metrics['load'] > 70 ? "'#ef4444'" : ($metrics['load'] < 30 ? "'#10b981'" : "'#3b82f6'") }},
                        '#f3f4f6'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '75%',
                rotation: -90,
                circumference: 180,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            }
        });
    </script>
</body>
</html>