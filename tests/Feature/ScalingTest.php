<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ScalingService;

class ScalingTest extends TestCase
{
    private ScalingService $scalingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scalingService = new ScalingService();
    }

    public function test_dashboard_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Auto Scaling Dashboard');
    }

    public function test_scale_up_on_high_load()
    {
        // Mock high load
        $this->scalingService->simulateLoad(85);
        
        $metrics = $this->scalingService->getCurrentMetrics();
        $this->assertGreaterThan(1, $metrics['workers']);
    }

    public function test_scale_down_on_low_load()
    {
        // Set initial high workers
        cache()->put('active_workers', 5, 3600);
        
        // Mock low load
        $this->scalingService->simulateLoad(15);
        
        $metrics = $this->scalingService->getCurrentMetrics();
        $this->assertLessThan(5, $metrics['workers']);
    }
}