<?php

namespace App\Imports;

use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

abstract class Import implements WithHeadingRow, ToCollection
{
    public $key;
    public $current = 2; // Row starts at line 2

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function pushError($error)
    {
        $cacheKey = $this->key . '_errors';
        $cachedErrors = Cache::get($cacheKey, []);
        $cachedErrors[] = [
            'line' => $this->current,
            'message' => $error,
            'type' => 'error',
            'timestamp' => now()->toISOString()
        ];
        Cache::put($cacheKey, $cachedErrors, now()->addMinutes(10));
    }

    public function pushSuccess($message)
    {
        $cacheKey = $this->key . '_success';
        $cachedSuccess = Cache::get($cacheKey, []);
        $cachedSuccess[] = [
            'line' => $this->current,
            'message' => $message,
            'type' => 'success',
            'timestamp' => now()->toISOString()
        ];
        Cache::put($cacheKey, $cachedSuccess, now()->addMinutes(10));
    }

    public function collection(Collection $rows)
    {
        $totalRows = $rows->count();
        
        // Initialize progress
        $this->initializeProgress($totalRows);

        foreach ($rows as $row) {
            $this->processRow($row);
            $this->current++;
            
            // Update overall progress
            $this->updateOverallProgress($totalRows);
        }

        // Mark as completed
        $this->markCompleted();
    }

    private function initializeProgress($totalRows)
    {
        $progressKey = $this->key . '_overall_progress';
        Cache::put($progressKey, [
            'total_rows' => $totalRows,
            'processed_rows' => 0,
            'percentage' => 0,
            'status' => 'processing',
            'started_at' => now()->toISOString()
        ], now()->addMinutes(10));
    }

    private function updateOverallProgress($totalRows)
    {
        $progressKey = $this->key . '_overall_progress';
        $processed = $this->current - 2; // Subtract 1 because current starts at 2
        $percentage = round(($processed / $totalRows) * 100, 2);
        
        Cache::put($progressKey, [
            'total_rows' => $totalRows,
            'processed_rows' => $processed,
            'percentage' => $percentage,
            'status' => 'processing',
            'updated_at' => now()->toISOString()
        ], now()->addMinutes(10));
    }

    private function markCompleted()
    {
        $progressKey = $this->key . '_overall_progress';
        $progress = Cache::get($progressKey, []);
        $progress['status'] = 'completed';
        $progress['completed_at'] = now()->toISOString();
        Cache::put($progressKey, $progress, now()->addMinutes(10));
    }

    abstract public function processRow($row);
}