<?php

namespace App\Http\Controllers;

use App\Services\ScalingService;
use Illuminate\Http\Request;

class ScalingController extends Controller
{
    private ScalingService $scalingService;

    public function __construct(ScalingService $scalingService)
    {
        $this->scalingService = $scalingService;
    }

    public function index()
    {
        $metrics = $this->scalingService->getCurrentMetrics();
        $history = $this->scalingService->getScalingHistory();
        
        return view('dashboard', [
            'metrics' => $metrics,
            'history' => $history,
            'scaleUpThreshold' => 70,
            'scaleDownThreshold' => 30,
        ]);
    }

    public function simulateLoad(Request $request)
    {
        $load = $request->input('load');
        $this->scalingService->simulateLoad($load);
        
        return redirect()->route('dashboard')
            ->with('success', 'Load simulated successfully!');
    }

    public function simulateLoadPattern()
    {
        // Simulate different load patterns
        $patterns = [
            'normal' => rand(20, 60),
            'high' => rand(70, 95),
            'low' => rand(5, 25),
            'spike' => rand(85, 100),
        ];
        
        $pattern = array_rand($patterns);
        $load = $patterns[$pattern];
        
        $this->scalingService->simulateLoad($load);
        
        return redirect()->route('dashboard')
            ->with('success', "Simulated {$pattern} load pattern ({$load}%)");
    }

    public function reset()
    {
        $this->scalingService->reset();
        
        return redirect()->route('dashboard')
            ->with('info', 'Scaling system reset to defaults');
    }

    public function metrics()
    {
        $metrics = $this->scalingService->getCurrentMetrics();
        
        return response()->json([
            'status' => 'success',
            'data' => $metrics,
            'timestamp' => now()->toISOString(),
        ]);
    }
}