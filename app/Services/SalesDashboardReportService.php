<?php

namespace App\Services;

use App\Exports\SalesDashboardReportExport;
use App\Models\Channel;
use App\Models\Concept;
use App\Models\SalesDashboardReport;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class SalesDashboardReportService {
    
    private $cacheKeyBase = '';

    private function getCacheKey($salesTable) {
        $today = Carbon::today()->toDateString(); 
        $cacheKey = "{$salesTable}_report__{$today}";
    
        return $cacheKey;
    }

    public function getData($salesTable)
    {
        try {
            // Retrieve data from cache
            $data = Cache::get($this->getCacheKey($salesTable), []);


            if(empty($data)){
               return $this->generateSalesReport($salesTable);
            }

			\Log::info(json_encode($data, JSON_PRETTY_PRINT));

            return $data; 
        } catch (\Exception $e) {
            Log::error('Cache Retrieval of Sales Dashboard Report Data Error: ' . $e->getMessage());
            return []; 
        }
    }
   
    public function generateSalesReport($salesTable){

        $data = Cache::lock('sales_report_lock', 5)->get(function () use ($salesTable) {
            // Generate and cache the data here
			
            $currentDay = date('d');    
            $currentMonth = date('m');
            $currentYear = date('Y'); 
            $previousYear = date('Y', strtotime('-1 year'));
            
            // $currentMonth = 3;
            // $previousYear = 2019;
            // $currentYear = 2020; 
            // $currentDay = 29;

            /* Logic:
                If today is January, three years of data will be available: the current year 
                will be used for daily and weekly sales, while the previous two years 
                will be used for monthly, quarterly, and year-to-date calculations.
                
                This is for scoping data in the getStoreSales method.
            */

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
                    self::processSalesData($salesTable, $yearData['year'], $currentMonth, $currentDay, $data);
                }
            }

            // dd($data);

            // Store the data in cache
            Cache::put($this->getCacheKey($salesTable), $data, now()->endOfDay());

            return $data;

        });

        if (is_null($data)) {
            Log::warning('Could not acquire lock for sales report generation.');
            
            return Cache::get($this->getCacheKey($salesTable), []); 
        }

        return $data; 

    }

    private function processDailySalesData($salesTable, $year, $month, $day, &$data, $salesDashboardReport) {

        $data['channel_codes']['TOTAL'][$year]['weeks'] = $salesDashboardReport->getSalesSummary($salesTable);

        $data['channel_codes']['TOTAL'][$year]['last_three_days'] = $salesDashboardReport->getSalesSummaryForLastThreeDays($salesTable);


        // Process sales per channel
        $sumPerChannel = $salesDashboardReport->getSalesWeeklyPerChannel($salesTable);

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
        $lastThreeDaysPerChannel = $salesDashboardReport->getSalesSummaryForLastThreeDaysPerChannel($salesTable);

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

        $lastThreeDaysDates = $salesDashboardReport->getLastThreeDaysDates("{$year}-{$month}-{$day}");

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

    private function processMonthlySalesData($salesTable, $year, &$data, $salesDashboardReport) {

        // Get and store sales summary
        $data['channel_codes']['TOTAL'][$year]['months'] = $salesDashboardReport->getSalesPerMonth($salesTable);

        // Process sales per channel
        $sumPerChannel = $salesDashboardReport->getSalesPerMonthByChannel($salesTable);

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

    private function processQuarterlySalesData($salesTable, $year, &$data, $salesDashboardReport) {

        // Get and store sales summary
        $data['channel_codes']['TOTAL'][$year]['quarters'] = $salesDashboardReport->getSalesPerQuarter($salesTable);

        // Process sales per channel
        $sumPerChannel = $salesDashboardReport->getSalesPerQuarterByChannel($salesTable);

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

    private function processSalesData($salesTable, $year, $month, $day, &$data) {
        $salesDashboardReport = new SalesDashboardReport(['year' => $year, 'month' => $month, 'day' => $day]);

        $salesDashboardReport->getSalesDataFrom($salesTable);

        self::processDailySalesData($salesTable,$year, $month, $day, $data, $salesDashboardReport);

        self::processMonthlySalesData($salesTable,$year, $data, $salesDashboardReport);

        self::processQuarterlySalesData($salesTable,$year, $data, $salesDashboardReport);
       
        // Get YTD
        $data['channel_codes']['TOTAL'][$year]['ytd'] = $salesDashboardReport->getYearToDate($salesTable);
    }

    public function updateYTDReport($salesTable, $updateData){
        $data = $this->getData($salesTable);

        $data['channel_codes']['TOTAL'][$updateData['prevYear']]['ytd'] = $updateData['prevData'];
        $data['channel_codes']['TOTAL'][$updateData['currYear']]['ytd'] = $updateData['currData'];

        // Update the data in cache
        Cache::put($this->getCacheKey($salesTable), $data, now()->endOfDay());
    }

    public function fetchData($salesTable)
    {

        $reloadData = request()->has('reload_data');

        $generatedData = $reloadData ? self::generateSalesReport($salesTable) : self::getData($salesTable);
        $month = $generatedData['yearData']['month'];

        if($month == 1){

            $prevYear = $generatedData['yearData']['nextPreviousYear'];
            $currYear = $generatedData['yearData']['previousYear'];

            $currYearForDaily = $generatedData['yearData']['currentYear'];

        } else {
            $prevYear = $generatedData['yearData']['previousYear'];
            $currYear = $generatedData['yearData']['currentYear'];
        }

        $channel_codes = $generatedData['channel_codes'];
        $lastThreeDaysDates = $generatedData['lastThreeDaysDates'];
        $concepts = Concept::get(['id', 'concept_name']);

        $hasDTC = $channel_codes['DTC'] ?? false;

        if ($hasDTC) {
            $channels = Channel::select(['id', 'channel_name'])->where('channel_code', 'DTC')->get();
            $salesTable = 'digits_sales';
        } else {
            $channels = Channel::get(['id', 'channel_name']);
            $salesTable = 'store_sales';
        }


        $data = [
            'channel_codes' => $channel_codes,
            'prevYear' => $prevYear,
            'currYear' => $currYear,
            'lastThreeDaysDates' => $lastThreeDaysDates,
        ];
        
        // Generate HTML for each tab using partial views
        $tab1Html = view('dashboard-report.partials.daily', [
            'channel_codes' => $channel_codes,
            'prevYear' => $month == 1 ? $currYear : $prevYear,
            'currYear' => $month == 1 ? $currYearForDaily : $currYear,
            'lastThreeDaysDates' => $lastThreeDaysDates,
        ])->render();
        
        $tab2Html = view('dashboard-report.partials.monthly',
         $data)->render();
         
        $tab3Html = view('dashboard-report.partials.quarterly',
         $data)->render();

        $tab4Html = view('dashboard-report.partials.ytd', [
            'channel_codes' => $channel_codes,
            'prevYear' => $prevYear,
            'currYear' => $currYear,
            'lastThreeDaysDates' => $lastThreeDaysDates,
            'month' => $month, 
            'channels' => $channels, 
            'concepts' => $concepts,
            'salesTable' => $salesTable,
            'selectedChannel' =>  Cache::get("{$salesTable}_selected_channel"),
            'selectedConcept' =>  Cache::get("{$salesTable}_selected_concept")
        ])->render();
    
        return response()->json([
            'tab1Html' => $tab1Html,
            'tab2Html' => $tab2Html,
            'tab3Html' => $tab3Html,
            'tab4Html' => $tab4Html,
        ]);
    }

    
    public function exportPDF($salesTable)
    {

        $data = [];
        $generatedData = self::getData($salesTable);

        if(empty($generatedData)){
            $generatedData = self::generateSalesReport($salesTable);
        }
        
        // Merge the generated data into the data array
        $data = array_merge($data, $generatedData);

        try {	
            // Load the view and generate the PDF
            $pdf = SnappyPdf::loadView('dashboard-report.exports.pdf-sales-report', $data)
                    ->setPaper('A4', 'landscape')
                    ->setOptions(['margin-top' => 35, 'margin-right' => 5, 'margin-bottom' => 10, 'margin-left' => 5]);
                
            // Return the PDF as a download
            Log::info('Data for PDF: ');
            Log::info(json_encode($data, JSON_PRETTY_PRINT));


            $dateTime = date('Y-m-d_H-i-s');
            $fileName = "{$salesTable}-report_{$dateTime}.pdf";

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            // Handle exceptions and log errors
            Log::error('PDF Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }


    public function showPDF(Request $request){
        $data = [];
        $data['page_title'] = 'Store Sales Dashboard Report';

        $generatedData = self::getData('digits_sales');

        if(empty($generatedData)){
            $generatedData = self::generateSalesReport('digits_sales');
        }

        $data = array_merge($data, $generatedData);

        return view('dashboard-report.store-sales.test-pdf', $data);
    }

    public function exportExcel($salesTable){

        $dateTime = date('Y-m-d_H-i-s');
        $fileName = "{$salesTable}-report_{$dateTime}.xlsx";

        $data = self::getData($salesTable);

        return Excel::download(new SalesDashboardReportExport($data), $fileName);
    }

    public function updateYtdSalesReport(Request $request, $salesTable)
    {
        $request->validate([
            'channel' => 'required|string',
            'concept' => 'required|string',
        ]);

        // Fetch data based on selected channel and concept
        $channelId = $request->input('channel');
        $conceptId = $request->input('concept');

        Cache::put("{$salesTable}_selected_channel", $channelId, now()->endOfDay());
        Cache::put("{$salesTable}_selected_concept", $conceptId, now()->endOfDay());

        // $currentMonth = 1;
        // $previousYear = 2021;
        // $currentYear = 2022; 
        // $currentDay = 7;
        
        $currentDay = date('d');
        $currentMonth = date('m');
        $previousYear = date('Y', strtotime('-1 year'));
        $currentYear = date('Y'); 

        if($currentMonth == 1){
            // $previousYear = 2020;
            // $currentYear = 2021;
            $previousYear =  date('Y', strtotime('-2 years'));
            $currentYear = date('Y', strtotime('-1 year'));

        } 

        $prevInstance = new SalesDashboardReport(['year' => $previousYear, 'month' => $currentMonth, 'day' => $currentDay]);
        $currInstance = new SalesDashboardReport(['year' => $currentYear, 'month' => $currentMonth, 'day' => $currentDay]);

        $prevData = $prevInstance->getYearToDateWithSelection($salesTable,$channelId, $conceptId);
        $currData = $currInstance->getYearToDateWithSelection($salesTable,$channelId, $conceptId);

        $updateCacheData = [
            'prevYear' => $previousYear,
            'currYear' => $currentYear,
            'prevData' => $prevData,
            'currData' => $currData,
        ];
        
        self::updateYTDReport($salesTable,$updateCacheData);

        $data = [
            'currApple' => $currData['APPLE']['sum_of_net_sales'],
            'currNonApple' => $currData['NON-APPLE']['sum_of_net_sales'],
            'currTotalApple' => $currData['TOTAL']['sum_of_net_sales'],

            'prevApple' => $prevData['APPLE']['sum_of_net_sales'],
            'prevNonApple' => $prevData['NON-APPLE']['sum_of_net_sales'],
            'prevTotalApple' => $prevData['TOTAL']['sum_of_net_sales'],
        ];

    
        return response()->json($data);
    }

}