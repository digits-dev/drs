<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class StoreSalesDashboardReportService {
    
    const CACHE_KEY = 'daily_sales_report';
    const CACHE_EXPIRATION = 60; // Cache for 2 hours

    public function generateData()
    {
        // Use a lock to prevent concurrent writes
        Cache::lock('generate_data_lock', 10)->get(function () {
            $data = []; // Generate your data here
            for ($i = 1; $i <= 5; $i++) {
                $data[] = $this->createDataItem($i);
            }

            // Store the data in cache
            Cache::put(self::CACHE_KEY, $data, self::CACHE_EXPIRATION);
        });
    }

    public function getData()
    {
        try {
            // Retrieve data from cache
            return Cache::get(self::CACHE_KEY, []); // Return an empty array if not found
        } catch (\Exception $e) {
            \Log::error('Cache retrieval error: ' . $e->getMessage());
            return []; // Fallback to an empty array or other default behavior
        }
    }

    private function createDataItem($id)
    {
        return [
            'id' => $id,
            'value' => "Data Item $id",
        ];
    }

    // Method to clear the cache (if needed)
    public function clearCache()
    {
        Cache::forget(self::CACHE_KEY); // Clear existing cache
    }
}