<?php

namespace App\Services;

use App\Models\StoreSalesDashboardReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StoreSalesDashboardReportService {
    
    private $cacheKeyBase = 'sales_report_';
    private function getCacheKey()
    {
        return $this->cacheKeyBase . date('Y-m-d');
    }

    private function getCacheExpiration()
    {
        // Get the current time
        $now = time();

        // Get the timestamp for the end of the day (midnight)
        $endOfDay = strtotime('tomorrow') - 1; 

        // Calculate the difference
        return $endOfDay - $now;
    }


    public function getData()
    {
        try {
            // Retrieve data from cache
            $data = Cache::get($this->getCacheKey(), []);


            if(empty($data)){
               return $this->generateSalesReport();
            }

			\Log::info(json_encode($data, JSON_PRETTY_PRINT));

            return $data; 
        } catch (\Exception $e) {
            Log::error('Cache Retrieval of Sales Dashboard Report Data Error: ' . $e->getMessage());
            return []; 
        }
    }
   
    // Method to clear the cache (if needed)
    public function clearCache()
    {
        Cache::forget($this->getCacheKey()); 
    }


    public function generateSalesReport(){

        $data = Cache::lock('sales_report_lock', 5)->get(function () {
            // Generate and cache the data here
			
            $currentDay = date('d');
            $currentMonth = date('m');
            $currentYear = date('Y'); 
            $previousYear = date('Y', strtotime('-1 year'));
            
            // $currentMonth = 3;
            // $previousYear = 2019;
            // $currentYear = 2020; 
            // $currentDay = 29;

            // $currentMonth = 2;
            // $previousYear = 2023;
            // $currentYear = 2024; 
            // $currentDay = 29;

            // $currentMonth = 2;
            // $previousYear = 2018;
            // $currentYear = 2019; 
            // $currentDay = 23;

            //bug
			// $currentMonth = 8;
			// $previousYear = 2021;
			// $currentYear = 2022; 
			// $currentDay = 7;

			// $currentMonth = 9;
			// $previousYear = 2021;
			// $currentYear = 2022; 
			// $currentDay = 21;

            if($currentMonth == 1){
                // $nextPreviousYear = 2020;
                // $previousYear = 2021;
                // $currentYear = 2022; 

                $nextPreviousYear =  date('Y', strtotime('-2 years'));
                $previousYear = date('Y', strtotime('-1 year'));
                $currentYear = date('Y'); 

            } else {
                // $nextPreviousYear = null;
                // $previousYear = 2021;
                // $currentYear = 2022; 

                $nextPreviousYear =  null;
                $previousYear = date('Y', strtotime('-1 year'));
                $currentYear = date('Y'); 
            }

            $years = [
                ['year' => $nextPreviousYear],
                ['year' => $previousYear],
                ['year' => $currentYear],
            ];
            
            $data = [
                'yearData' => [
                    'nextPreviousYear' => $years[0]['year'],
                    'previousYear' => $years[1]['year'],
                    'currentYear' => $years[2]['year'],
                    'month' => $currentMonth,
                ],
                'channel_codes' => [],
            ];

            $data['lastThreeDaysDates'] = self::getLastThreeDaysOrDates('date', "{$currentYear}-{$currentMonth}-{$currentDay}");

            foreach ($years as $yearData) {
                if($yearData['year']){
                    self::processSalesData($yearData['year'], $currentMonth, $currentDay, $data);
                }
            }

            // dd($data);

            // Store the data in cache
            Cache::put($this->getCacheKey(), $data, $this->getCacheExpiration());

            return $data;

        });

        if (is_null($data)) {
            Log::warning('Could not acquire lock for daily sales report generation.');
            
            return Cache::get($this->getCacheKey(), []); 
        }

        return $data; 

    }

    private function processDailySalesData($year, $month, $day, &$data, $storeSalesDR) {

        $data['channel_codes']['TOTAL'][$year]['weeks'] = $storeSalesDR->getSalesSummary();

        $data['channel_codes']['TOTAL'][$year]['last_three_days'] = $storeSalesDR->getSalesSummaryForLastThreeDays();


        // Process sales per channel
        $sumPerChannel = $storeSalesDR->getSalesWeeklyPerChannel();

        foreach ($sumPerChannel as $sale) {
   
            $channelCode = $sale['channel_classification'];


            if (!isset($data['channel_codes'][$channelCode])) {
                $data['channel_codes'][$channelCode] = [];
            }

            $data['channel_codes'][$channelCode][$year]['weeks'][$sale['week_cutoff']] = [
                'sum_of_net_sales' => $sale['sum_of_net_sales'],
            ];
        }
        
        
        // Last three days per channel
        $lastThreeDaysPerChannel = $storeSalesDR->getSalesSummaryForLastThreeDaysPerChannel();

        foreach ($lastThreeDaysPerChannel as $sale) {
            $channelCode = $sale['channel_classification'];

            if (!isset($data['channel_codes'][$channelCode])) {
                $data['channel_codes'][$channelCode] = [];
            }

            $data['channel_codes'][$channelCode][$year]['last_three_days'][] = [
                'date_of_the_day' => $sale['date_of_the_day'],
                'day' => $sale['day'],
                'sum_of_net_sales' => $sale['sum_of_net_sales'],
            ];
        }

        $lastThreeDaysDates = $storeSalesDR->getLastThreeDaysDates("{$year}-{$month}-{$day}");

        // Now add entries for any missing dates with a sum_of_net_sales of 0
        foreach ($lastThreeDaysDates as $date) {
            foreach ($data['channel_codes'] as $channelCode => &$years) {
                if (!isset($years[$year]['last_three_days'])) {
                    $years[$year]['last_three_days'] = [];
                }

                // Check if the date already exists in last_three_days
                $exists = false;
                foreach ($years[$year]['last_three_days'] as $entry) {
                    if ($entry['date_of_the_day'] === $date) {
                        $exists = true;
                        break;
                    }
                }

                // If it doesn't exist, add it with a sum_of_net_sales of 0
                if (!$exists) {
                    $years[$year]['last_three_days'][] = [
                        'date_of_the_day' => $date,
                        'sum_of_net_sales' => 0,
                    ];
                }
            }
        }

        // Sort by date 
        foreach ($data['channel_codes'] as $channelCode => &$years) {
            foreach ($years as $year => &$yearData) {
                if (isset($yearData['last_three_days'])) {
                    usort($yearData['last_three_days'], function($a, $b) {
                        return strtotime($a['date_of_the_day']) - strtotime($b['date_of_the_day']);
                    });
                }
            }
        }

    }

    private function getLastThreeDaysOrDates($type = 'day', $date = null)
    {
        // Use the provided date or default to today
        $today = $date ? Carbon::parse($date) : Carbon::today();
        
        // Initialize an array to hold the last three previous dates
        $lastThreeDays = [];
        
        // If today is the 1st, 2nd, or 3rd, include those days
        if ($today->day <= 3) {
            for ($i = 0; $i < $today->day; $i++) {
                $day = $today->copy()->subDays($i);
                $lastThreeDays[] = $day; // Store as Carbon objects
            }
        } else {
            // Get the last three days prior to the provided date
            for ($i = 1; $i <= 3; $i++) {
                $day = $today->copy()->subDays($i);
                $lastThreeDays[] = $day; // Store as Carbon objects
            }
        }

        // Sort the array of Carbon objects in ascending order
        usort($lastThreeDays, function($a, $b) {
            return $a->gt($b) ? 1 : -1;
        });
        
        // Format the dates for output
        $formattedDays = [];
        foreach ($lastThreeDays as $day) {
            // $formattedDays[] = $type === 'date' ? $day->format('d-M') : $day->format('D');
            $formattedDays[$day->format('d-M')] = $day->format('D');
        }

        return $formattedDays;
    }

    private function processMonthlySalesData($year, &$data, $storeSalesDR) {

        // Get and store sales summary
        $data['channel_codes']['TOTAL'][$year]['months'] = $storeSalesDR->getSalesPerMonth();

        // Process sales per channel
        $sumPerChannel = $storeSalesDR->getSalesPerMonthByChannel();

        foreach ($sumPerChannel as $sale) {
      
            $channelCode = $sale['channel_classification'];

            if (!isset($data['channel_codes'][$channelCode])) {
                $data['channel_codes'][$channelCode] = [];
            }

            $data['channel_codes'][$channelCode][$year]['months'][$sale['month_cutoff']] = [
                'sum_of_net_sales' => $sale['sum_of_net_sales'],
            ];
        }
    }

    private function processQuarterlySalesData($year, &$data, $storeSalesDR) {

        // Get and store sales summary
        $data['channel_codes']['TOTAL'][$year]['quarters'] = $storeSalesDR->getSalesPerQuarter();

        // Process sales per channel
        $sumPerChannel = $storeSalesDR->getSalesPerQuarterByChannel();

        foreach ($sumPerChannel as $sale) {

            $channelCode = $sale['channel_classification'];


            if (!isset($data['channel_codes'][$channelCode])) {
                $data['channel_codes'][$channelCode] = [];
            }

            $data['channel_codes'][$channelCode][$year]['quarters'][$sale['quarter_cutoff']] = [
                'sum_of_net_sales' => $sale['sum_of_net_sales'],
            ];
        }
    }

    private function processSalesData($year, $month, $day, &$data) {
        $storeSalesDR = new StoreSalesDashboardReport(['year' => $year, 'month' => $month, 'day' => $day]);

        $test = $storeSalesDR->getStoreSalesData();

        // $count = count($test);
        // dump("year $year", $count);
        // dump(array_slice($test, 0, 10));

        self::processDailySalesData($year, $month, $day, $data, $storeSalesDR);

        self::processMonthlySalesData($year, $data, $storeSalesDR);

        self::processQuarterlySalesData($year, $data, $storeSalesDR);
       
        // Get YTD
        $data['channel_codes']['TOTAL'][$year]['ytd'] = $storeSalesDR->getYearToDate();
    }

    public function updateYTDReport($updateData){
        $data = $this->getData();

        $data['channel_codes']['TOTAL'][$updateData['prevYear']]['ytd'] = $updateData['prevData'];
        $data['channel_codes']['TOTAL'][$updateData['currYear']]['ytd'] = $updateData['currData'];

        // Update the data in cache
        Cache::put($this->getCacheKey(), $data, $this->getCacheExpiration());
    }
}