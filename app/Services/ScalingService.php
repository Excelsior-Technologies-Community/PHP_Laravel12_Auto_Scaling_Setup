<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScalingService
{
    private int $maxWorkers = 10;
    private int $minWorkers = 1;
    private int $scaleUpThreshold = 70;
    private int $scaleDownThreshold = 30;
    private int $cooldownPeriod = 5;

    /**
     * Current Dashboard Metrics
     */

    public function getCurrentMetrics(): array
    {
        $workers = Cache::get('active_workers', 1);

        $load = Cache::get('current_load', 0);

        $queue = $this->getQueueSize();

        $memory = round(memory_get_usage(true) / 1024 / 1024, 2);

        $health = $this->getWorkerHealth(
            $load,
            $queue,
            $memory
        );

        $efficiency = $this->getWorkerEfficiency(
            $workers,
            $load
        );

        $utilization = $this->getQueueUtilization(
            $queue
        );

        $recommendation = $this->getRecommendation(
            $load,
            $queue,
            $workers
        );

        return [

            'workers' => $workers,

            'load' => $load,

            'requests_per_minute' => $this->calculateRequestsPerMinute(),

            'average_response_time' => Cache::get(
                'avg_response_time',
                rand(40, 250)
            ),

            'memory_usage' => $memory,

            'last_scaled' => Cache::get(
                'last_scaled_at',
                'Never'
            ),

            'queue_size' => $queue,

            /*
        |--------------------------------------------------------------------------
        | New Metrics
        |--------------------------------------------------------------------------
        */

            'worker_health' => $health,

            'worker_efficiency' => $efficiency,

            'queue_utilization' => $utilization,

            'recommendation' => $recommendation['title'],

            'recommendation_type' => $recommendation['type'],

            'recommendation_reason' => $recommendation['reason'],

        ];
    }

    /**
     * Simulate Server Load
     */
    public function simulateLoad(?int $load = null): void
    {
        $load = $load ?? rand(5, 100);

        Cache::put('current_load', $load, now()->addMinutes(10));

        $this->recordMetrics($load);

        $this->autoScale($load);
    }

    /**
     * Auto Scaling Logic
     */
    private function autoScale(int $load): void
    {
        $currentWorkers = Cache::get('active_workers', 1);

        $lastScaleTime = Cache::get('last_scaled_at_timestamp', 0);

        if ((time() - $lastScaleTime) < $this->cooldownPeriod) {
            return;
        }

        $newWorkers = $currentWorkers;
        $action = 'maintain';
        $reason = 'Load within threshold';

        if (
            $load > $this->scaleUpThreshold &&
            $currentWorkers < $this->maxWorkers
        ) {
            $newWorkers++;

            $action = 'scale_up';

            $reason = "Load {$load}% exceeded {$this->scaleUpThreshold}%";
        } elseif (
            $load < $this->scaleDownThreshold &&
            $currentWorkers > $this->minWorkers
        ) {
            $newWorkers--;

            $action = 'scale_down';

            $reason = "Load {$load}% below {$this->scaleDownThreshold}%";
        }

        if ($newWorkers != $currentWorkers) {

            Cache::put('active_workers', $newWorkers, now()->addHours(1));

            Cache::put(
                'last_scaled_at',
                now()->toDateTimeString(),
                now()->addHours(1)
            );

            Cache::put(
                'last_scaled_at_timestamp',
                time(),
                now()->addHours(1)
            );
        }

        // Log every simulation
        $this->logScalingAction(
            $currentWorkers,
            $newWorkers,
            $load,
            $action,
            $reason
        );
    }

    /**
     * Store Scaling History
     */
    private function logScalingAction(
        int $currentWorkers,
        int $newWorkers,
        int $load,
        string $action,
        string $reason
    ): void {

        DB::table('scaling_logs')->insert([

            'current_workers' => $currentWorkers,

            'new_workers' => $newWorkers,

            'load_percentage' => $load,

            'action' => $action,

            'reason' => $reason,

            'created_at' => now(),

            'updated_at' => now(),
        ]);
    }

    /**
     * Requests Per Minute
     */
    private function calculateRequestsPerMinute(): int
    {
        $requests = Cache::get('request_log', []);

        $lastMinute = Carbon::now()->subMinute()->timestamp;

        return count(array_filter($requests, function ($time) use ($lastMinute) {

            return $time >= $lastMinute;
        }));
    }

    /**
     * Record Requests
     */
    private function recordMetrics(int $load): void
    {
        $requests = Cache::get('request_log', []);

        $requests[] = time();

        if (count($requests) > 1000) {
            $requests = array_slice($requests, -1000);
        }

        Cache::put('request_log', $requests, now()->addHour());

        $response = match (true) {

            $load >= 90 => rand(250, 450),

            $load >= 70 => rand(150, 250),

            $load >= 40 => rand(80, 150),

            default => rand(20, 80),
        };

        Cache::put(
            'avg_response_time',
            $response,
            now()->addHour()
        );
    }

    /**
     * Simulated Queue Size
     */
    private function getQueueSize(): int
    {
        $load = Cache::get('current_load', 0);

        if ($load >= 90) {
            return rand(80, 100);
        }

        if ($load >= 70) {
            return rand(60, 85);
        }

        if ($load >= 40) {
            return rand(25, 60);
        }

        return rand(0, 20);
    }

    /**
     * Get Scaling History with Search, Filter & Pagination
     */
    public function getScalingHistory(
        string $search = '',
        string $action = '',
        string $date = '',
        int $perPage = 5
    ) {
        $query = DB::table('scaling_logs');

        // Search
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('load_percentage', 'like', "%{$search}%")
                    ->orWhere('current_workers', 'like', "%{$search}%")
                    ->orWhere('new_workers', 'like', "%{$search}%");
            });
        }

        // Filter by action
        if (!empty($action)) {
            $query->where('action', $action);
        }

        // Filter by date
        if (!empty($date)) {
            $query->whereDate('created_at', $date);
        }

        return $query
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * Dashboard Statistics
     */
    public function getStatistics(): array
    {
        return [

            'total_logs' => DB::table('scaling_logs')->count(),

            'scale_up' => DB::table('scaling_logs')
                ->where('action', 'scale_up')
                ->count(),

            'scale_down' => DB::table('scaling_logs')
                ->where('action', 'scale_down')
                ->count(),

            'maintain' => DB::table('scaling_logs')
                ->where('action', 'maintain')
                ->count(),

            'average_load' => round(
                DB::table('scaling_logs')
                    ->avg('load_percentage'),
                2
            ),

            'max_load' => DB::table('scaling_logs')
                ->max('load_percentage'),

            'min_load' => DB::table('scaling_logs')
                ->min('load_percentage'),

            'latest_action' => DB::table('scaling_logs')
                ->latest()
                ->value('action'),

            'latest_scaled_at' => DB::table('scaling_logs')
                ->latest()
                ->value('created_at'),
        ];
    }

    /**
     * Delete Single History Record
     */
    public function deleteHistory(int $id): bool
    {
        return DB::table('scaling_logs')
            ->where('id', $id)
            ->delete();
    }

    /**
     * Delete All History
     */
    public function deleteAllHistory(): bool
    {
        DB::table('scaling_logs')->truncate();

        return true;
    }

    /**
     * Get Export Data
     */
    public function getExportData()
    {
        return DB::table('scaling_logs')
            ->select(
                'id',
                'current_workers',
                'new_workers',
                'load_percentage',
                'action',
                'reason',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Worker Health Status
     */
    private function getWorkerHealth(
        int $load,
        int $queue,
        float $memory
    ): string {

        if ($load >= 85 || $queue >= 90 || $memory >= 120) {
            return 'Overloaded';
        }

        if ($load >= 60 || $queue >= 60 || $memory >= 80) {
            return 'Busy';
        }

        return 'Healthy';
    }

    /**
     * Worker Efficiency
     */
    private function getWorkerEfficiency(
        int $workers,
        int $load
    ): int {

        if ($workers <= 0) {
            return 0;
        }

        return min(
            100,
            round($load / $workers)
        );
    }

    /**
     * Queue Utilization
     */
    private function getQueueUtilization(
        int $queue
    ): int {

        return min(
            100,
            round(($queue / 100) * 100)
        );
    }

    /**
     * Scaling Recommendation
     */
    private function getRecommendation(
        int $load,
        int $queue,
        int $workers
    ): array {

        if (
            $load > $this->scaleUpThreshold ||
            $queue > 80
        ) {

            return [

                'title' => 'Scale Up Recommended',

                'type' => 'up',

                'reason' =>
                "High load ({$load}%) or queue ({$queue}) detected."

            ];
        }

        if (
            $load < $this->scaleDownThreshold &&
            $queue < 20 &&
            $workers > $this->minWorkers
        ) {

            return [

                'title' => 'Scale Down Recommended',

                'type' => 'down',

                'reason' =>
                "Low load ({$load}%) and queue ({$queue})."

            ];
        }

        return [

            'title' => 'No Action Needed',

            'type' => 'normal',

            'reason' =>
            'System is operating within configured thresholds.'

        ];
    }

    /**
     * Reset Auto Scaling Demo
     */
    public function reset(): void
    {
        Cache::forget('active_workers');
        Cache::forget('current_load');
        Cache::forget('request_log');
        Cache::forget('last_scaled_at');
        Cache::forget('last_scaled_at_timestamp');
        Cache::forget('avg_response_time');
    }
}
