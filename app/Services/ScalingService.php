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
    private int $cooldownPeriod = 60; // seconds

    public function getCurrentMetrics(): array
    {
        return [
            'workers' => Cache::get('active_workers', 1),
            'load' => Cache::get('current_load', 0),
            'requests_per_minute' => $this->calculateRequestsPerMinute(),
            'average_response_time' => Cache::get('avg_response_time', 0),
            'memory_usage' => memory_get_usage(true) / 1024 / 1024, // MB
            'last_scaled' => Cache::get('last_scaled_at', 'Never'),
            'queue_size' => $this->getQueueSize(),
        ];
    }

    public function simulateLoad(int $load = null): void
    {
        $load = $load ?? rand(10, 100);
        Cache::put('current_load', $load, 120);
        
        $this->recordMetrics($load);
        $this->autoScale($load);
    }

    private function autoScale(int $load): void
    {
        $currentWorkers = Cache::get('active_workers', 1);
        $lastScaleTime = Cache::get('last_scaled_at_timestamp', 0);
        
        // Cooldown check
        if (time() - $lastScaleTime < $this->cooldownPeriod) {
            return;
        }

        $action = 'maintain';
        $newWorkers = $currentWorkers;
        $reason = 'Load within thresholds';

        if ($load > $this->scaleUpThreshold && $currentWorkers < $this->maxWorkers) {
            $newWorkers = $currentWorkers + 1;
            $action = 'scale_up';
            $reason = "Load ({$load}%) exceeds threshold ({$this->scaleUpThreshold}%)";
        } elseif ($load < $this->scaleDownThreshold && $currentWorkers > $this->minWorkers) {
            $newWorkers = $currentWorkers - 1;
            $action = 'scale_down';
            $reason = "Load ({$load}%) below threshold ({$this->scaleDownThreshold}%)";
        }

        if ($newWorkers !== $currentWorkers) {
            Cache::put('active_workers', $newWorkers, 3600);
            Cache::put('last_scaled_at', Carbon::now()->toDateTimeString(), 3600);
            Cache::put('last_scaled_at_timestamp', time(), 3600);
            
            $this->logScalingAction($currentWorkers, $newWorkers, $load, $action, $reason);
            
            // Simulate worker adjustment delay
            sleep(1);
        }
    }

    private function logScalingAction(int $current, int $new, int $load, string $action, string $reason): void
    {
        DB::table('scaling_logs')->insert([
            'current_workers' => $current,
            'new_workers' => $new,
            'load_percentage' => $load,
            'action' => $action,
            'reason' => $reason,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function calculateRequestsPerMinute(): int
    {
        $requests = Cache::get('request_log', []);
        $oneMinuteAgo = Carbon::now()->subMinute()->timestamp;
        
        return count(array_filter($requests, function($time) use ($oneMinuteAgo) {
            return $time > $oneMinuteAgo;
        }));
    }

    private function recordMetrics(int $load): void
    {
        $requests = Cache::get('request_log', []);
        $requests[] = time();
        
        // Keep only last 1000 requests
        if (count($requests) > 1000) {
            $requests = array_slice($requests, -1000);
        }
        
        Cache::put('request_log', $requests, 3600);
    }

    private function getQueueSize(): int
    {
        return rand(0, 100); // Simulated queue size
    }

    public function getScalingHistory(int $limit = 10): array
    {
        return DB::table('scaling_logs')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function reset(): void
    {
        Cache::forget('active_workers');
        Cache::forget('current_load');
        Cache::forget('request_log');
        Cache::forget('last_scaled_at');
        Cache::forget('last_scaled_at_timestamp');
    }
}