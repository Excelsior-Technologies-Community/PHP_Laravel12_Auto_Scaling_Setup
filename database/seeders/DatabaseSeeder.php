<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('scaling_logs')->insert([
            [
                'current_workers' => 1,
                'new_workers' => 2,
                'load_percentage' => 75,
                'action' => 'scale_up',
                'reason' => 'Load (75%) exceeds threshold (70%)',
                'created_at' => now()->subMinutes(30),
                'updated_at' => now()->subMinutes(30),
            ],
            [
                'current_workers' => 2,
                'new_workers' => 3,
                'load_percentage' => 82,
                'action' => 'scale_up',
                'reason' => 'Load (82%) exceeds threshold (70%)',
                'created_at' => now()->subMinutes(25),
                'updated_at' => now()->subMinutes(25),
            ],
        ]);
    }
}