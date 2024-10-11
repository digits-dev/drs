<?php

namespace App\Services;

use App\Models\StoreSalesDashboardReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StoreSalesDashboardReportService {
    
    private $cacheKeyBase = 'daily_sales_report_';
    const CACHE_EXPIRATION = 3600; // Cache for 1 hour

    private function getCacheKey()
    {
        return $this->cacheKeyBase . date('Y-m-d');
    }


    public function getData()
    {
        try {
            // Retrieve data from cache
            $data = Cache::get($this->getCacheKey(), []);

            if(empty($data)){
               return $this->generateDailySalesReport();
            }

            return $data; 
        } catch (\Exception $e) {
            \Log::error('Cache Retrieval of Sales Dashboard Report Data Error: ' . $e->getMessage());
            return []; // Fallback to an empty array or other default behavior
        }
    }
   
    // Method to clear the cache (if needed)
    public function clearCache()
    {
        Cache::forget($this->getCacheKey()); // Clear existing cache
    }


    public function generateDailySalesReport(){

        $data = Cache::lock('daily_sales_report_lock', 5)->get(function () {
            // Generate and cache the data here
     
			
            // $currentDay = date('d');
            // $currentMonth = date('m');
            // $currentYear = date('Y'); 
            // $previousYear = date('Y', strtotime('-1 year'));
            
            // $currentMonth = 3;
            // $previousYear = 2019;
            // $currentYear = 2020; 
            // $currentDay = 29;

            $currentMonth = 2;
            $previousYear = 2021;
            $currentYear = 2022; 
            $currentDay = 23;

            
			// $currentMonth = 8;
			// $previousYear = 2023;
			// $currentYear = 2024; 
			// $currentDay = 30;


            $years = [
                ['year' => $previousYear, 'month' => $currentMonth],
                ['year' => $currentYear, 'month' => $currentMonth],
            ];
            
            $data = [
                'yearData' => [
                    'previousYear' => $years[0]['year'],
                    'currentYear' => $years[1]['year'],
                ],
                'channel_codes' => [],
            ];

            $data['lastThreeDaysDates'] = self::getLastThreeDaysOrDates('date', "{$currentYear}-{$currentMonth}-{$currentDay}");

            foreach ($years as $yearData) {
                self::processYearData($yearData['year'], $yearData['month'], $currentDay, $data);
            }

            // dd($data['channel_codes']);

            // Store the data in cache
            Cache::put($this->getCacheKey(), $data, self::CACHE_EXPIRATION);

            return $data;

        });

        // Check if data is null (indicating lock was not acquired)
        if (is_null($data)) {
            // Handle the case when the lock was not acquired
            \Log::warning('Could not acquire lock for daily sales report generation.');
            
            // Optionally, you can return an empty array or existing cached data
            return Cache::get($this->getCacheKey(), []); // Return cached data if available
        }

        return $data; // Return the generated data

    }

    private function processYearData($year, $month, $day, &$data) {
        $storeSalesDR = new StoreSalesDashboardReport(['year' => $year, 'month' => $month, 'day' => $day]);

        // Create temp table and get summary
        $storeSalesDR->createTempTable();

        // Get and store sales summary
        $data['channel_codes']['TOTAL'][$year]['weeks'] = $storeSalesDR->getSalesSummary()->toArray();

        // Last three days summary
        $data['channel_codes']['TOTAL'][$year]['last_three_days'] = $storeSalesDR->getSalesSummaryForLastThreeDays();

        
        // Process sales per channel
        $sumPerChannel = $storeSalesDR->getSalesWeeklyPerChannel();


        foreach ($sumPerChannel as $sale) {
            // if($sale['channel_classification'] == 'OTHER'){
            // 	dump($sale['week_cutoff']);
            // 	dump($sale['min_reference_number']);
            // }
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
        

        // Drop the temporary table
        $storeSalesDR->dropTempTable();
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

    
    public static function generateQuickChart($chartType, $isPerChannel, $dataCategory, $channelCodes, $lastThreeDays, $year = null,  $previousYear = null, $currentYear = null){

        if($isPerChannel) {
            $maxValue = self::calculateMaxValues($dataCategory, $channelCodes, $lastThreeDays, $previousYear, $currentYear)[$dataCategory];
            $buffer = $maxValue * 0.15; 
            $maxValWithBuffer = round(($maxValue + $buffer) / 1000000) * 1000000; //To make the max val with ending 000,000

            $chartData = self::generateDataPerChannel($chartType,$isPerChannel,  $dataCategory, $year, $channelCodes, $lastThreeDays, $maxValWithBuffer);
        } else {
            $chartData = self::generateDataForOverallTotal($chartType,$isPerChannel,  $dataCategory, $previousYear, $currentYear, $channelCodes, $lastThreeDays);
        }

        // Encode the chart configuration as a JSON string
        $chartConfigJson = json_encode($chartData);
        
        // Create the QuickChart URL with specified width and height
        $width = 1000;  
        $height = 600;

        $quickChartUrl = 'https://quickchart.io/chart?c=' . urlencode($chartConfigJson) . '&width=' . $width . '&height=' . $height;

        return $quickChartUrl;
    }

    private static function generateDataForOverallTotal($chartType, $isPerChannel = false, $dataCategory = 'total', $prevYear, $currYear, $channelCodes, $lastThreeDaysDates ) {
        $labels = [];
        $pieLabels = [];
        $keyDates = $lastThreeDaysDates !== null ? array_keys($lastThreeDaysDates) : [];

    
        // LABELS
        switch ($dataCategory) {
            case 'total':
                $labels = ['TOTAL'];
                break;
            case 'weekly':
                $labels = ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
                break;
            case 'last_three_days':
                $labels = $keyDates;
                break;
            default:
                $labels = [];
        }
    
        // Function to generate data for each year

        $prevData = self::getDataForOverall($prevYear, $channelCodes, $dataCategory, $isPerChannel);
    
        $currData = self::getDataForOverall($currYear, $channelCodes, $dataCategory, $isPerChannel);

    
        // Pie data generation
        $pieData = [];
        foreach ($channelCodes as $channelCode => $channelData) {
            $dataStorage = [];
            $weeksPrev = $channelData[$prevYear]['weeks'] ?? [];
            $lastThreeDaysPrev = $channelData[$prevYear]['last_three_days'] ?? [];
            $weeksCurr = $channelData[$currYear]['weeks'] ?? [];
            $lastThreeDaysCurr = $channelData[$currYear]['last_three_days'] ?? [];
    
            if (!$isPerChannel && $channelCode === 'TOTAL') {
                $keys = [];
                switch ($dataCategory) {
                    case 'total':
                        $keys = ['TOTAL'];
                        $pieLabels = ["$prevYear TOTAL", "$currYear TOTAL"];
                        break;
                    case 'weekly':
                        $keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        $pieLabels = [
                            "$prevYear WK01", "$currYear WK01",
                            "$prevYear WK02", "$currYear WK02",
                            "$prevYear WK03", "$currYear WK03",
                            "$prevYear WK04", "$currYear WK04",
                        ];
                        break;
                }
    
                if ($dataCategory === 'last_three_days') {
                    foreach ($lastThreeDaysPrev as $day) {
                        $dataStorage[] = $day['sum_of_net_sales'] ?? 0;
                    }
                    foreach ($lastThreeDaysCurr as $day) {
                        $dataStorage[] = $day['sum_of_net_sales'] ?? 0;
                    }
                    foreach ($keyDates as $date) {
                        $pieLabels[] = "$prevYear $date";
                        $pieLabels[] = "$currYear $date";
                    }
                } else {
                    foreach ($keys as $key) {
                        $dataStorage[] = $weeksPrev[$key]['sum_of_net_sales'] ?? 0;
                    }
                    foreach ($keys as $key) {
                        $dataStorage[] = $weeksCurr[$key]['sum_of_net_sales'] ?? 0;
                    }
                }
    
                $pieData[] = [
                    'label' => $channelCode,
                    'data' => $dataStorage,
                    'borderWidth' => 2,
                    'fill' => false,
                ];
            }
        }

        // Chart js version 2.9.4

        return [
            'type' => $chartType,
            'data' => [
                'labels' => $chartType == 'pie' ? $pieLabels : $labels,
                'datasets' => $chartType == 'pie' ? $pieData : array_merge($prevData, $currData),
            ],
            'options' => [
                'layout' => ['padding' => 20],
                'title' => [
                    'display' => true,
                    'text' => "Sales Data",
                    'fontSize' => 16,
                    'padding' => 20,
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'boxWidth' => 10
                    ]
                ],
                'plugins' => [
                    'tickFormat' => [
                        'locale' => 'en-US',
                        'useGrouping' => true,
                        'applyToDataLabels' => true,
                    ],
                    "datalabels" => [
                        "display" => $chartType == 'pie' ? true : false,
                        "anchor" =>  $chartType == 'pie' ? "center" : "end",
                        "align" =>  $chartType == 'pie' ? "center" : "end",
                        "color" => "#000",
                    ],
                ],
            ],
        ];
    }
    
    private static function getDataForOverall($year, $channelCodes, $dataCategory, $isPerChannel) {
        $dataStorage = [];
        foreach ($channelCodes as $channelCode => $channelData) {
            $weeks = $channelData[$year]['weeks'] ?? [];
            $lastThreeDays = $channelData[$year]['last_three_days'] ?? [];

            if (!$isPerChannel && $channelCode === 'TOTAL') {
                $keys = [];
                switch ($dataCategory) {
                    case 'total':
                        $keys = ['TOTAL'];
                        break;
                    case 'weekly':
                        $keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        break;
                }

                if ($dataCategory === 'last_three_days') {
                    foreach ($lastThreeDays as $day) {
                        $dataForChannel[]  = $day['sum_of_net_sales'] ?? 0;
                    }
                } else {
                    foreach ($keys as $key) {
                        $dataForChannel[]  = $weeks[$key]['sum_of_net_sales'] ?? 0;
                    }
                }

                // return [
                // 	'label' => "$year $channelCode",
                // 	'data' => $dataStorage,
                // 	'borderWidth' => 2,
                // ];

                // Create dataset entry
                $dataStorage[] = [
                    'label' => "$year $channelCode",
                    'data' => $dataForChannel,
                    'borderWidth' => 2,
                    'fill' => false,
                ];
            }
        }
        return $dataStorage;
    }

    private static function generateDataPerChannel($chartType, $isPerChannel = true, $dataCategory = 'total', $year, $channelCodes, $lastThreeDaysDates, $yScaleMaxVal) {
        $labels = [];
        $datasets = [];
        $keyDates = $lastThreeDaysDates !== null ? array_keys($lastThreeDaysDates) : [];
    
        // LABELS
        switch ($dataCategory) {
            case 'total':
                $labels = ['TOTAL'];
                break;
            case 'weekly':
                $labels = ['WEEK 1', 'WEEK 2', 'WEEK 3', 'WEEK 4'];
                break;
            case 'last_three_days':
                $labels = $keyDates;
                break;
            default:
                $labels = [];
        }

        $newData = [];

        foreach ($channelCodes as $channelCode => $channelData) {
            $dataStorage = [];
            $weeks = $channelData[$year]['weeks'] ?? [];
            $lastThreeDaysData = $channelData[$year]['last_three_days'] ?? [];
    
            // Channel code mapping
            switch ($channelCode) {
                case 'TOTAL-RTL':
                    $channelCode = 'RETAIL';
                    break;
                case 'DLR/CRP':
                    $channelCode = 'OUT';
                    break;
                case 'FRA-DR':
                    $channelCode = 'FRA';
                    break;
            }
    
            if ($isPerChannel && $channelCode && $channelCode !== "TOTAL") {
                $keys = [];
    
                switch ($dataCategory) {
                    case 'total':
                        $keys = ['TOTAL'];
                        break;
                    case 'weekly':
                        $keys = ['WK01', 'WK02', 'WK03', 'WK04'];
                        break;
                }
    
                if ($dataCategory === 'last_three_days') {
                    foreach ($lastThreeDaysData as $day) {
                        $netSales = $day['sum_of_net_sales'] ?? 0;
                        $dataStorage[] = $netSales;
                    }
                } else {
                    foreach ($keys as $key) {
                        $netSales = $weeks[$key]['sum_of_net_sales'] ?? 0;
                        $dataStorage[] = $netSales;
                    }
                }
    
                $maxVal = !empty($dataStorage) ? max($dataStorage) : 0;
    
                $newData[] = [
                    'label' => $channelCode,
                    'data' => $dataStorage,
                    'borderWidth' => 2,
                    'maxVal' => $maxVal,
                    'fill' => false,
                ];
            }
        }

        // Chart js version 2.9.4

        return [
            'type' => $chartType,
            'data' => [
                'labels' => $labels,
                'datasets' => $newData,
            ],
            'options' => [
                'layout' => ['padding' => 20],
                'title' => [
                    'display' => true,
                    'text' => "{$year} Sales Data",
                    'fontSize' => 16,
                    'padding' => 20
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'boxWidth' => 10
                    ]
                ],
                'plugins' => [
                    'tickFormat' => [
                        'locale' => 'en-US',
                        'useGrouping' => true,
                        'applyToDataLabels' => true,
                    ],
                    // "datalabels" => [
                    // 	"anchor" => "end",
                    // 	"align" => "end",
                    // 	"color" => "#000",
                    // ],
                    "datalabels" => [
                        "display" => $chartType == 'pie' ? true : false,
                        "anchor" =>  $chartType == 'pie' ? "center" : "end",
                        "align" =>  $chartType == 'pie' ? "center" : "end",
                        "color" => "#000",
                    ],
                ],
                'scales' => [
                    'yAxes' => [[
                        'display' => $chartType !== 'pie',
                        'ticks'=> [
                            'max' => $yScaleMaxVal, 
                            // 'beginAtZero' => true,
                            // 'color' => '#000'
                        ],]
                    ],
                ],
            ],
        ];
    }
    

    private static function calculateMaxValues($categoryVal, $channelCodes, $lastThreeDays, $prevYear, $currYear) {
        $maxValues = [];
    
        $chartConfigs = [
            ['year' => $prevYear, 'type' => 'line', 'category' => $categoryVal],
            ['year' => $currYear, 'type' => 'line', 'category' => $categoryVal],
        ];
    
        foreach ($chartConfigs as $config) {
            // Assuming you have a method generateDataPerChannel that returns the same structure
            $chartData = self::generateDataPerChannel($config['type'], true, $config['category'], $config['year'], $channelCodes, $lastThreeDays, 0);

            
            $dataEntries = $chartData['data']['datasets'];
    
            foreach ($dataEntries as $dataset) {
                $maxVal = $dataset['maxVal'] ?? 0;
    
                if (!isset($maxValues[$config['category']]) || $maxVal > $maxValues[$config['category']]) {
                    $maxValues[$config['category']] = $maxVal;
                }
            }
        }
    
        return $maxValues;
    }

}