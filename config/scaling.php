<?php

return [
    'auto_scaling' => [
        'max_workers' => 10,
        'min_workers' => 1,
        'scale_up_threshold' => 70,
        'scale_down_threshold' => 30,
        'cooldown_period' => 60,
        'check_interval' => 5,
    ],
    
    'metrics' => [
        'retention_period' => 3600,
        'sample_rate' => 1,
    ],
];