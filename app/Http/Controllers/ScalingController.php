<?php

namespace App\Http\Controllers;

use App\Services\ScalingService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ScalingController extends Controller
{
    protected ScalingService $scalingService;

    public function __construct(ScalingService $scalingService)
    {
        $this->scalingService = $scalingService;
    }

    /**
     * Dashboard
     */
    public function index(Request $request)
    {
        $metrics = $this->scalingService->getCurrentMetrics();

        $history = $this->scalingService->getScalingHistory(
            $request->search ?? '',
            $request->action ?? '',
            $request->date ?? '',
            5
        );

        $statistics = $this->scalingService->getStatistics();

        return view('dashboard', [

            'metrics' => $metrics,

            'history' => $history,

            'statistics' => $statistics,

            'scaleUpThreshold' => 70,

            'scaleDownThreshold' => 30,

            'search' => $request->search,

            'action' => $request->action,

            'date' => $request->date,

            /*
        |--------------------------------------------------------------------------
        | New Dashboard Data
        |--------------------------------------------------------------------------
        */

            'workerHealth' => $metrics['worker_health'],

            'workerEfficiency' => $metrics['worker_efficiency'],

            'queueUtilization' => $metrics['queue_utilization'],

            'recommendation' => $metrics['recommendation'],

            'recommendationType' => $metrics['recommendation_type'],

            'recommendationReason' => $metrics['recommendation_reason'],

        ]);
    }

    /**
     * Random / Custom Load
     */
    public function simulateLoad(Request $request)
    {
        $request->validate([
            'load' => 'nullable|integer|min:1|max:100',
        ]);

        $load = $request->input('load');

        $this->scalingService->simulateLoad($load);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Load simulated successfully.');
    }

    /**
     * Pattern Load
     */
    public function simulateLoadPattern()
    {
        $patterns = [

            'Normal' => rand(25, 55),

            'High' => rand(70, 90),

            'Low' => rand(5, 20),

            'Traffic Spike' => rand(90, 100),

        ];

        $pattern = array_rand($patterns);

        $load = $patterns[$pattern];

        $this->scalingService->simulateLoad($load);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                "{$pattern} Load ({$load}%) simulated successfully."
            );
    }

    /**
     * Reset Demo
     */
    public function reset()
    {
        $this->scalingService->reset();

        return redirect()
            ->route('dashboard')
            ->with('success', 'System reset successfully.');
    }

    /**
     * Live Metrics API
     */
    public function metrics()
    {
        $metrics = $this->scalingService->getCurrentMetrics();

        return response()->json([

            'status' => true,

            'metrics' => $metrics,

            'statistics' => $this->scalingService->getStatistics(),

            'health' => $metrics['worker_health'],

            'efficiency' => $metrics['worker_efficiency'],

            'queue_utilization' => $metrics['queue_utilization'],

            'recommendation' => [

                'title' => $metrics['recommendation'],

                'type' => $metrics['recommendation_type'],

                'reason' => $metrics['recommendation_reason'],

            ],

            'generated_at' => now()->toDateTimeString(),

        ]);
    }

    /**
     * Export Scaling History as CSV
     */
    public function exportCsv(): StreamedResponse
    {
        $logs = $this->scalingService->getExportData();

        $fileName = 'scaling_history_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($logs) {

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'ID',
                'Current Workers',
                'New Workers',
                'Load (%)',
                'Action',
                'Reason',
                'Created At',
            ]);

            foreach ($logs as $log) {

                fputcsv($file, [
                    $log->id,
                    $log->current_workers,
                    $log->new_workers,
                    $log->load_percentage,
                    $log->action,
                    $log->reason,
                    $log->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete Single History Record
     */
    public function deleteHistory(int $id)
    {
        $deleted = $this->scalingService->deleteHistory($id);

        if ($deleted) {
            return redirect()
                ->route('dashboard')
                ->with('success', 'History record deleted successfully.');
        }

        return redirect()
            ->route('dashboard')
            ->with('error', 'History record not found.');
    }

    /**
     * Delete All History
     */
    public function deleteAllHistory()
    {
        $this->scalingService->deleteAllHistory();

        return redirect()
            ->route('dashboard')
            ->with('success', 'All scaling history deleted successfully.');
    }
}
