<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Log;

class StoreSalesDashboardReport extends Model
{
    use HasFactory;

    protected $table = 'temp_store_sales';
    public $timestamps = false;
    protected $fillable = ['year', 'month', 'day'];
    protected $year;
    protected $rawMonth;
    protected $month;
    protected $day;
    protected $yearMonth;
    protected $startDate;
    protected $endDate;
    protected $currentDayAsDate;

    public function __construct(array $attributes = []){
        parent::__construct($attributes);
        
        // Initialize year and month, and set properties
        if (isset($attributes['year']) && isset($attributes['month']) && isset($attributes['day'])) {

            $this->year = $attributes['year'];
            $month = str_pad($attributes['month'], 2, '0', STR_PAD_LEFT);
            $this->day = str_pad($attributes['day'], 2, '0', STR_PAD_LEFT);  


            if($month == '01') {
                // $currentYear = date("Y");
                $currentYear = 2022;

                if($currentYear == $attributes['year']){
                    $this->month = $month;
                } else {
                    $this->month = 12;
                    $this->previousMonth = 12;
                }
            } else {
                $this->month = $month;
                $previousMonthRaw = $attributes['month'] - 1;
                $this->previousMonth = str_pad($previousMonthRaw, 2, '0', STR_PAD_LEFT); ;
            }
            
            $this->currentDayAsDate = "{$this->year}-{$this->month}-{$this->day}";
            $this->yearMonth = "{$this->year}-{$this->month}";
            $this->startDate = "{$this->year}-{$this->month}-01";
            $this->endDate = date("Y-m-d", strtotime("last day of {$this->startDate}"));
           
        }

        Log::info("Year $this->year");
        Log::info("RAW Month $this->rawMonth");
        Log::info("Previous Month $this->previousMonth");
        Log::info("Month $this->month");
        Log::info("Day $this->day");

        Log::info("Current Day as Date $this->currentDayAsDate");
        Log::info("YearMonth $this->yearMonth");
        Log::info("StartDate $this->startDate");
        Log::info("EndDate $this->endDate");

    }

    //FIRST APPROACH
    public function createTempTableBackUp(){

        DB::statement("
            CREATE TEMPORARY TABLE temp_store_sales AS
            SELECT 
                store_sales.sales_date,
                store_sales.net_sales,
                store_sales.reference_number,
                channels.channel_code
            FROM store_sales 
            LEFT JOIN channels ON store_sales.channels_id = channels.id
            LEFT JOIN customers ON store_sales.customers_id = customers.id
            LEFT JOIN all_items ON store_sales.item_code = all_items.item_code
            LEFT JOIN concepts ON customers.concepts_id = concepts.id
            WHERE store_sales.is_final = 1 
                AND YEAR(store_sales.sales_date) = {$this->year}
                AND MONTH(store_sales.sales_date) BETWEEN 1 AND {$this->month}
                AND store_sales.quantity_sold > 0
                AND store_sales.net_sales IS NOT NULL
                AND store_sales.sold_price > 0
                AND store_sales.channels_id != 12 
        ");
    // AND MONTH(store_sales.sales_date) BETWEEN 1 AND {$this->rawMonth}

    // AND store_sales.sales_date BETWEEN '{$this->startDate}' AND '{$this->endDate}'

    // AND store_sales.channels_id != 12  = EEE is not included 

    }

    public function dropTempTableBackUp(){
        DB::statement('DROP TEMPORARY TABLE IF EXISTS temp_store_sales');
    }
    public function getSalesSummaryBackUp()
    {

        return self::select(DB::raw("
            CASE 
                WHEN sales_date BETWEEN '{$this->startDate}' AND '{$this->yearMonth}-07' THEN 'WK01'
                WHEN sales_date BETWEEN '{$this->yearMonth}-08' AND '{$this->yearMonth}-14' THEN 'WK02'
                WHEN sales_date BETWEEN '{$this->yearMonth}-15' AND '{$this->yearMonth}-21' THEN 'WK03'
                WHEN sales_date BETWEEN '{$this->yearMonth}-22' AND '{$this->endDate}' THEN 'WK04'
            END AS week_cutoff,
            SUM(net_sales) AS sum_of_net_sales
        "))
        ->whereBetween('sales_date', [$this->startDate, $this->endDate])
        ->groupBy(DB::raw('week_cutoff WITH ROLLUP'))
        ->get()->map(function($item) {
            if(is_null($item->week_cutoff)){
                $item->week_cutoff = 'TOTAL';
            }
            return $item;
        })->keyBy('week_cutoff');

    }

    public function getSalesWeeklyPerChannelBackUp()
    {

        return self::select(DB::raw("
            CASE 
                WHEN sales_date BETWEEN '{$this->startDate}' AND '{$this->yearMonth}-07' THEN 'WK01'
                WHEN sales_date BETWEEN '{$this->yearMonth}-08' AND '{$this->yearMonth}-14' THEN 'WK02'
                WHEN sales_date BETWEEN '{$this->yearMonth}-15' AND '{$this->yearMonth}-21' THEN 'WK03'
                WHEN sales_date BETWEEN '{$this->yearMonth}-22' AND '{$this->endDate}' THEN 'WK04'
            END AS week_cutoff,

            CASE 
                WHEN channel_code = 'ONL' THEN 'ECOMM'
                WHEN channel_code IN ('DLR', 'CRP', 'OUT') THEN 'DLR/CRP'
                WHEN channel_code = 'RTL' THEN 'TOTAL-RTL'
                WHEN channel_code = 'FRA' THEN 'FRA-DR'
                WHEN channel_code = 'SVC' THEN 'SC'
                WHEN channel_code = 'CON' THEN 'CON'
                ELSE 'OTHER'
            END AS channel_classification,

            SUM(net_sales) AS sum_of_net_sales,
            MIN(reference_number) AS min_reference_number
        "))
        ->whereBetween('sales_date', [$this->startDate, $this->endDate])
        ->groupBy('channel_classification', DB::raw('week_cutoff WITH ROLLUP'))
        ->orderByRaw("FIELD(channel_classification, 'ECOMM', 'TOTAL-RTL', 'SC', 'DLR/CRP', 'CON', 'FRA-DR', 'OTHER')")
        ->get()->map(function($item){
            if(is_null($item->week_cutoff)){
                $item->week_cutoff = 'TOTAL';
            }
            return $item;
        });
    }

    public function getSalesSummaryForLastThreeDaysBackUp()
    {

        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);

        $salesSummary  = self::select(
            DB::raw("
                DATE_FORMAT(sales_date, '%Y-%m-%d') AS date_of_the_day,
                DATE_FORMAT(sales_date, '%a') AS day,
                SUM(net_sales) AS sum_of_net_sales
            "),
            )
            ->whereIn('sales_date', $lastThreeDays) 
            ->groupBy('sales_date') 
            ->get()->sortBy('date_of_the_day')->keyBy('date_of_the_day');

        // Prepare the final summary
        $summary = [];
        
        foreach ($lastThreeDays as $date) {
            $formattedDate = date('Y-m-d', strtotime($date));
            if (isset($salesSummary[$formattedDate])) {
                $summary[] =  [
                    'date_of_the_day' => $salesSummary[$formattedDate]->date_of_the_day,
                    'sum_of_net_sales' => $salesSummary[$formattedDate]->sum_of_net_sales,
                    'day' => date('D', strtotime($date)),
                ];
            } else {
                // If no sales found for this date, add a placeholder with 0
                $summary[] =  [
                    'date_of_the_day' => $formattedDate,
                    'sum_of_net_sales' => 0,
                    'day' => date('D', strtotime($date)),
                ];
            }
        }

        return $summary;
    }

    public function getSalesSummaryForLastThreeDaysPerChannelBackUp()
    {

        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);

        $salesSummary = self::select(
            DB::raw("
            DATE_FORMAT(sales_date, '%Y-%m-%d') AS date_of_the_day,
            DATE_FORMAT(sales_date, '%a') AS day,
            SUM(net_sales) AS sum_of_net_sales,
            MIN(reference_number) as min_reference_number,
            CASE 
                WHEN channel_code = 'ONL' THEN 'ECOMM'
                WHEN channel_code IN ('DLR', 'CRP', 'OUT') THEN 'DLR/CRP' -- Include 'OUT' if needed, or else it become others
                WHEN channel_code = 'RTL' THEN 'TOTAL-RTL'
                WHEN channel_code = 'FRA' THEN 'FRA-DR'
                WHEN channel_code = 'SVC' THEN 'SC'
                WHEN channel_code = 'CON' THEN 'CON'
                ELSE 'OTHER'
            END AS channel_classification
            
            "),
        )
        ->whereIn('sales_date', $lastThreeDays) 
        ->groupBy('sales_date', 'channel_classification') 
        ->get()->sortBy('date_of_the_day');

        return $salesSummary;
    }

    public function getSalesPerMonthBackUp()
    {
        return DB::table('temp_store_sales')->select(DB::raw("
            CONCAT('M', LPAD(MONTH(sales_date), 2, '0')) AS month_cutoff,
            SUM(net_sales) AS sum_of_net_sales
        "))
        ->whereBetween(DB::raw('MONTH(sales_date)'),[1, $this->previousMonth])
        ->groupBy(DB::raw("month_cutoff WITH ROLLUP"))
        ->get()->map(function($item) {
            if(is_null($item->month_cutoff)){
                $item->month_cutoff = 'TOTAL';
            }
            return (array) $item;
        })->keyBy('month_cutoff');
    }

    public function getSalesPerMonthByChannelBackUp()
    {
        return DB::table('temp_store_sales')->select(DB::raw("
            CONCAT('M', LPAD(MONTH(sales_date), 2, '0')) AS month_cutoff,
            CASE 
                WHEN channel_code = 'ONL' THEN 'ECOMM'
                WHEN channel_code IN ('DLR', 'CRP', 'OUT') THEN 'DLR/CRP'
                WHEN channel_code = 'RTL' THEN 'TOTAL-RTL'
                WHEN channel_code = 'FRA' THEN 'FRA-DR'
                WHEN channel_code = 'SVC' THEN 'SC'
                WHEN channel_code = 'CON' THEN 'CON'
                ELSE 'OTHER'
            END AS channel_classification,
            SUM(net_sales) AS sum_of_net_sales,
            MIN(reference_number) AS min_reference_number
        "))
        ->whereBetween(DB::raw('MONTH(sales_date)'),[1, $this->previousMonth])
        ->groupBy('channel_classification', DB::raw('month_cutoff WITH ROLLUP'))
        ->orderByRaw("FIELD(channel_classification, 'ECOMM', 'TOTAL-RTL', 'SC', 'DLR/CRP', 'CON', 'FRA-DR', 'OTHER')")
        ->get()->map(function($item){
            if(is_null($item->month_cutoff)){
                $item->month_cutoff = 'TOTAL';
            }
            return (array) $item;
        });
    }

    public function getSalesPerQuarterBackUp()
    {
        return DB::table('temp_store_sales')->select(DB::raw("
            CONCAT('Q', QUARTER(sales_date)) AS quarter_cutoff,
            SUM(net_sales) AS sum_of_net_sales
        "))
        ->whereBetween(DB::raw('MONTH(sales_date)'),[1, $this->previousMonth])
        ->groupBy(DB::raw("quarter_cutoff WITH ROLLUP"))
        ->get()->map(function($item) {
            if (is_null($item->quarter_cutoff)) {
                $item->quarter_cutoff = 'TOTAL';
            }
            return (array) $item;
        })->keyBy('quarter_cutoff');
    }

    public function getSalesPerQuarterByChannelBackUp()
    {
        return DB::table('temp_store_sales')->select(DB::raw("
            CONCAT('Q', QUARTER(sales_date)) AS quarter_cutoff,
            CASE 
                WHEN channel_code = 'ONL' THEN 'ECOMM'
                WHEN channel_code IN ('DLR', 'CRP', 'OUT') THEN 'DLR/CRP'
                WHEN channel_code = 'RTL' THEN 'TOTAL-RTL'
                WHEN channel_code = 'FRA' THEN 'FRA-DR'
                WHEN channel_code = 'SVC' THEN 'SC'
                WHEN channel_code = 'CON' THEN 'CON'
                ELSE 'OTHER'
            END AS channel_classification,
            SUM(net_sales) AS sum_of_net_sales,
            MIN(reference_number) AS min_reference_number
        "))
        ->whereBetween(DB::raw('MONTH(sales_date)'),[1, $this->previousMonth])
        ->groupBy('channel_classification', DB::raw('quarter_cutoff WITH ROLLUP'))
        ->orderByRaw("FIELD(channel_classification, 'ECOMM', 'TOTAL-RTL', 'SC', 'DLR/CRP', 'CON', 'FRA-DR', 'OTHER')")
        ->get()->map(function($item){
            if(is_null($item->quarter_cutoff)){
                $item->quarter_cutoff = 'TOTAL';
            }
            return (array) $item;
        });
    }

    public function getYearToDateBackUp()
    {

        return DB::table('temp_store_sales')->select(DB::raw("
            CASE 
                WHEN brand_description = 'APPLE' THEN 'APPLE'
                ELSE 'NON-APPLE'
            END AS category,
            SUM(net_sales) AS sum_of_net_sales
        "))
        ->whereBetween(DB::raw('MONTH(sales_date)'),[1, $this->previousMonth])
        ->groupBy(DB::raw('category WITH ROLLUP'))
        ->get()->map(function($item) {
            if(is_null($item->category)){
                $item->category = 'TOTAL';
            }
            return $item;
        })->keyBy('category');

    }

    public function getYearToDateWithSelectionBackUp(){
        return DB::table('store_sales', 'ss')::select(DB::raw("
            CASE 
                WHEN ai.brand_description = 'APPLE' THEN 'APPLE'
                ELSE 'NON-APPLE'
            END AS category,
            MIN(c.channel_code) AS channel_code,
            MIN(co.concept_name) AS concept_name,
            SUM(ss.net_sales) AS sum_of_net_sales
        "))
        ->leftJoin('channels as c', 'ss.channels_id', 'c.id')
        ->leftJoin('customers as cu', 'ss.customers_id', 'cu.id')
        ->leftJoin('all_items as ai', 'ss.item_code', 'ai.item_code')
        ->leftJoin('concepts as co', 'cu.concepts_id', 'co.id')
        ->where('ss.is_final', 1)
        ->whereYear('ss.sales_date', $this->year)
        ->whereBetween(DB::raw('MONTH(ss.sales_date)'), [1, $this->previousMonth])
        ->where('ss.channels_id', 6)
        ->where('cu.concepts_id', 324)
        ->where('ss.quantity_sold', '>', 0)
        ->whereNotNull('ss.net_sales')
        ->where('ss.sold_price', '>', 0)
        ->where('ss.channels_id', '!=', 12)
        ->groupBy(DB::raw('category WITH ROLLUP'))
        ->get()->map(function($item) {
            if(is_null($item->category)){
                $item->category = 'TOTAL';
            }
            return $item;
        })->keyBy('category');
    }
    
    public function getLastThreeDaysDates($date = null)
    {

        // for testing in date feb 29
        // $date = '2023-02-29';

        $notLeapYear = false;
        
        // Use the provided date or default to today
        $today = $date ? Carbon::parse($date) : Carbon::today();

        // Check if the date is February 29 in a non-leap year
        if ($today->month === 3 && $today->day === 1 && !$today->isLeapYear() ) {
            // Adjust to February 28
            $today = Carbon::createFromDate($today->year, 2, 28);
            $notLeapYear = true;
        }
        
        // Initialize an array to hold the last three previous dates
        $lastThreeDays = [];
    
        // If today is the 1st, 2nd, or 3rd, include those days
        if ($today->day <= 3) {
            for ($i = 0; $i < $today->day; $i++) {
                $day = $today->copy()->subDays($i);

                $lastThreeDays[] =  "{$this->yearMonth}-{$day->format('d')}";

            }
        } else {
            if($notLeapYear){
                for ($i = 0; $i < 3; $i++) {
                    $day = $today->copy()->subDays($i);

                    $lastThreeDays[] =  "{$this->yearMonth}-{$day->format('d')}";

                }
            } else {
                // Get the last three days prior to the provided date
                for ($i = 1; $i <= 3; $i++) {
                    $day = $today->copy()->subDays($i);

                    $lastThreeDays[] =  "{$this->yearMonth}-{$day->format('d')}";

                }
            }
            
          
        }
    
        // Sort the array in ascending order
        sort($lastThreeDays);
        
        return $lastThreeDays;
    }


     //SECOND APPROACH

     public function getStoreSalesData() {
        
        $cacheKey = $this->getCacheKey();

        Cache::forget($cacheKey); 

        // dump('stores', $this->year);
        // dump('stores', $this->month);

        // Cache the results of the query
        $storeSalesData = Cache::remember($cacheKey, $this->getCacheExpiration(), function() {
            return DB::select("
                SELECT 
                    store_sales.sales_date,
                    store_sales.net_sales,
                    store_sales.reference_number,
                    channels.channel_code,
                    store_sales.channels_id,
                    customers.concepts_id,
                    all_items.brand_description,
                    concepts.concept_name
                FROM store_sales 
                LEFT JOIN channels ON store_sales.channels_id = channels.id
                LEFT JOIN customers ON store_sales.customers_id = customers.id
                LEFT JOIN all_items ON store_sales.item_code = all_items.item_code
                LEFT JOIN concepts ON customers.concepts_id = concepts.id
                WHERE store_sales.is_final = 1 
                    AND YEAR(store_sales.sales_date) = {$this->year}
                    AND MONTH(store_sales.sales_date) BETWEEN 1 AND {$this->month}
                    AND store_sales.quantity_sold > 0
                    AND store_sales.net_sales IS NOT NULL
                    AND store_sales.sold_price > 0
                    AND store_sales.channels_id != 12 
            ");
        });
    
        return $storeSalesData;
    }

    public function getSalesSummary()
    {

        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);
        $lastDay = end($lastThreeDays);

        $dataCollection = $this->getDataCollection();

        // Group the data by week cutoff
        $salesSummary = $dataCollection->filter(function($row) use($lastDay) {

            if($lastDay <= $this->endDate) {
                return $row->sales_date >= $this->startDate && $row->sales_date <= $lastDay;
            } else {
                return $row->sales_date >= $this->startDate && $row->sales_date <= $this->endDate;
            }

        })->map(function($row) {
            $weekCutoff = null;

            if ($row->sales_date >= $this->startDate && $row->sales_date <= "{$this->yearMonth}-07") {
                $weekCutoff = 'WK01';
            } elseif ($row->sales_date >= "{$this->yearMonth}-08" && $row->sales_date <= "{$this->yearMonth}-14") {
                $weekCutoff = 'WK02';
            } elseif ($row->sales_date >= "{$this->yearMonth}-15" && $row->sales_date <= "{$this->yearMonth}-21") {
                $weekCutoff = 'WK03';
            } elseif ($row->sales_date >= "{$this->yearMonth}-22" && $row->sales_date <= $this->endDate) {
                $weekCutoff = 'WK04';
            }

            return [
                'week_cutoff' => $weekCutoff,
                'net_sales' => $row->net_sales,
            ];
        })->groupBy('week_cutoff')->map(function($group) {
            return [
                'sum_of_net_sales' => $group->sum('net_sales'),
            ];
        });

        // Add total row
        $salesSummary->put('TOTAL', [
            'sum_of_net_sales' => $salesSummary->sum(function($item) {
                return $item['sum_of_net_sales'];
            }),
        ]);

        return $salesSummary;
    }   

    public function getSalesWeeklyPerChannel()
    {
        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);
        $lastDay = end($lastThreeDays);

        $dataCollection = $this->getDataCollection();

        // Group the data by week and channel classification
        $salesSummary = $dataCollection->filter(function($row) use($lastDay) {

            if($lastDay <= $this->endDate) {
                return $row->sales_date >= $this->startDate && $row->sales_date <= $lastDay;
            } else {
                return $row->sales_date >= $this->startDate && $row->sales_date <= $this->endDate;
            }

        })->map(function($row) {
            // Determine week cutoff
            $weekCutoff = null;

            if ($row->sales_date >= $this->startDate && $row->sales_date <= "{$this->yearMonth}-07") {
                $weekCutoff = 'WK01';
            } elseif ($row->sales_date >= "{$this->yearMonth}-08" && $row->sales_date <= "{$this->yearMonth}-14") {
                $weekCutoff = 'WK02';
            } elseif ($row->sales_date >= "{$this->yearMonth}-15" && $row->sales_date <= "{$this->yearMonth}-21") {
                $weekCutoff = 'WK03';
            } elseif ($row->sales_date >= "{$this->yearMonth}-22" && $row->sales_date <= $this->endDate) {
                $weekCutoff = 'WK04';
            }

            // Determine channel classification
            switch ($row->channel_code) {
                case 'ONL':
                    $channelClassification = 'ECOMM';
                    break;
                case 'DLR':
                case 'CRP':
                case 'OUT':
                    $channelClassification = 'DLR/CRP';
                    break;
                case 'RTL':
                    $channelClassification = 'TOTAL-RTL';
                    break;
                case 'FRA':
                    $channelClassification = 'FRA-DR';
                    break;
                case 'SVC':
                    $channelClassification = 'SC';
                    break;
                case 'CON':
                    $channelClassification = 'CON';
                    break;
                default:
                    $channelClassification = 'OTHER';
                    break;
            }

            return [
                'week_cutoff' => $weekCutoff,
                'channel_classification' => $channelClassification,
                'net_sales' => $row->net_sales,
                'reference_number' => $row->reference_number,
            ];
        })->groupBy(function($item) {
            return "{$item['week_cutoff']}_{$item['channel_classification']}";
        })->map(function($group) {
            return [
                'sum_of_net_sales' => $group->sum('net_sales'),
                'min_reference_number' => $group->min('reference_number'),
            ];
        });

        // Prepare final result with ROLLUP equivalent (if needed)
        $finalResult = collect();
        foreach ($salesSummary as $key => $summary) {
            [$weekCutoff, $channelClassification] = explode('_', $key);
            $finalResult->push(array_merge($summary, [
                'week_cutoff' => $weekCutoff,
                'channel_classification' => $channelClassification,
            ]));
        }

        // Add totals for each classification
        $totalSummary = $finalResult->groupBy('channel_classification')->map(function($group) {
            return [
                'sum_of_net_sales' => $group->sum('sum_of_net_sales'),
                'min_reference_number' => $group->min('min_reference_number'),
            ];
        })->toArray();

        // Append total summary to the final result
        foreach ($totalSummary as $classification => $totals) {
            $finalResult->push(array_merge($totals, [
                'week_cutoff' => 'TOTAL',
                'channel_classification' => $classification,
            ]));
        }

        return $finalResult->values(); // Reset keys and return
    }

    public function getSalesSummaryForLastThreeDays()
    {
        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);

        $dataCollection = $this->getDataCollection();

        // Prepare the sales summary
        $salesSummary = [];

        foreach ($lastThreeDays as $date) {
            $formattedDate = date('Y-m-d', strtotime($date));
            $dailySales = $dataCollection->filter(function ($row) use ($formattedDate) {
                return $row->sales_date === $formattedDate;
            });

            $sumOfNetSales = $dailySales->sum('net_sales');

            $salesSummary[] = [
                'date_of_the_day' => $formattedDate,
                'sum_of_net_sales' => $sumOfNetSales,
                'day' => date('D', strtotime($date)),
            ];
        }

        return $salesSummary;
    }

    public function getSalesSummaryForLastThreeDaysPerChannel()
    {
        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);

        $dataCollection = $this->getDataCollection();


        // Prepare the sales summary
        $salesSummary = $dataCollection->filter(function ($row) use ($lastThreeDays) {
            return in_array($row->sales_date, $lastThreeDays);
        })->map(function ($row) {
                switch ($row->channel_code) {
                    case 'ONL':
                        $channelClassification = 'ECOMM';
                        break;
                    case 'DLR':
                    case 'CRP':
                    case 'OUT':
                        $channelClassification = 'DLR/CRP';
                        break;
                    case 'RTL':
                        $channelClassification = 'TOTAL-RTL';
                        break;
                    case 'FRA':
                        $channelClassification = 'FRA-DR';
                        break;
                    case 'SVC':
                        $channelClassification = 'SC';
                        break;
                    case 'CON':
                        $channelClassification = 'CON';
                        break;
                    default:
                        $channelClassification = 'OTHER';
                        break;
                }

            return [
                'date_of_the_day' => $row->sales_date,
                'day' => date('D', strtotime($row->sales_date)),
                'sum_of_net_sales' => $row->net_sales,
                'min_reference_number' => $row->reference_number,
                'channel_classification' => $channelClassification,
            ];
        });

        // Group by date and channel classification
        $groupedSummary = $salesSummary->groupBy(function ($item) {
            return $item['date_of_the_day'] . '_' . $item['channel_classification'];
        })->map(function ($group) {
            return [
                'date_of_the_day' => $group->first()['date_of_the_day'],
                'day' => $group->first()['day'],
                'sum_of_net_sales' => $group->sum('sum_of_net_sales'),
                'min_reference_number' => $group->min('min_reference_number'),
                'channel_classification' => $group->first()['channel_classification'],
            ];
        });

        return $groupedSummary->values(); // Reset keys and return
    }

    public function getSalesPerMonth()
    {
        $dataCollection = $this->getDataCollection();

        // Group by month and calculate sums
        $salesSummary = $dataCollection->filter(function ($row) {
            return $row->sales_date >= "{$this->year}-01-01" && $row->sales_date <= "{$this->year}-{$this->previousMonth}-31";
        })->groupBy(function ($row) {
            return 'M' . str_pad(date('m', strtotime($row->sales_date)), 2, '0', STR_PAD_LEFT);
        })->map(function ($group) {
            return [
                'sum_of_net_sales' => $group->sum('net_sales'),
            ];
        });

        // Prepare final result with ROLLUP equivalent
        $finalResult = $salesSummary->toArray();
        $totalSales = array_sum(array_column($finalResult, 'sum_of_net_sales'));

        // Add total row
        $finalResult['TOTAL'] = [
            'sum_of_net_sales' => $totalSales,
        ];

        return collect($finalResult);
    }

    public function getSalesPerMonthByChannel()
    {

        $dataCollection = $this->getDataCollection();


        // Group by month and channel classification
        $salesSummary = $dataCollection->filter(function ($row) {
            return $row->sales_date >= "{$this->year}-01-01" && $row->sales_date <= "{$this->year}-{$this->previousMonth}-31";
        })->map(function ($row) {
                switch ($row->channel_code) {
                    case 'ONL':
                        $channelClassification = 'ECOMM';
                        break;
                    case 'DLR':
                    case 'CRP':
                    case 'OUT':
                        $channelClassification = 'DLR/CRP';
                        break;
                    case 'RTL':
                        $channelClassification = 'TOTAL-RTL';
                        break;
                    case 'FRA':
                        $channelClassification = 'FRA-DR';
                        break;
                    case 'SVC':
                        $channelClassification = 'SC';
                        break;
                    case 'CON':
                        $channelClassification = 'CON';
                        break;
                    default:
                        $channelClassification = 'OTHER';
                        break;
                }

            return [
                'month_cutoff' => 'M' . str_pad(date('m', strtotime($row->sales_date)), 2, '0', STR_PAD_LEFT),
                'channel_classification' => $channelClassification,
                'net_sales' => $row->net_sales,
                'reference_number' => $row->reference_number,
            ];
        })->groupBy(function ($item) {
            return "{$item['month_cutoff']}_{$item['channel_classification']}";
        })->map(function ($group) {
            return [
                'month_cutoff' => $group->first()['month_cutoff'],
                'channel_classification' => $group->first()['channel_classification'],
                'sum_of_net_sales' => $group->sum('net_sales'),
                'min_reference_number' => $group->min('reference_number'),
            ];
        });

        // Prepare final result with ROLLUP equivalent
        $finalResult = collect();
        foreach ($salesSummary as $key => $summary) {
            [$monthCutoff, $channelClassification] = explode('_', $key);
            $finalResult->push(array_merge($summary, [
                'month_cutoff' => $monthCutoff,
                'channel_classification' => $channelClassification,
            ]));
        }

        // Add totals for each classification
        $totalSummary = $finalResult->groupBy('channel_classification')->map(function ($group) {
            return [
                'sum_of_net_sales' => $group->sum('sum_of_net_sales'),
                'min_reference_number' => $group->min('min_reference_number'),
            ];
        })->toArray();

        // Append total summary to the final result
        foreach ($totalSummary as $classification => $totals) {
            $finalResult->push(array_merge($totals, [
                'month_cutoff' => 'TOTAL',
                'channel_classification' => $classification,
            ]));
        }

        return $finalResult->values(); // Reset keys and return
    }


    public function getSalesPerQuarter()
    {

        $dataCollection = $this->getDataCollection();

        // Filter and group by quarter
        $salesSummary = $dataCollection->filter(function ($row) {
            return $row->sales_date >= "{$this->year}-01-01" && $row->sales_date <= "{$this->year}-{$this->previousMonth}-31";
        })->groupBy(function ($row) {
            return 'Q' . ceil(date('n', strtotime($row->sales_date)) / 3);
        })->map(function ($group) {
            return [
                'sum_of_net_sales' => $group->sum('net_sales'),
            ];
        });

        // Prepare final result with ROLLUP equivalent
        $finalResult = $salesSummary->toArray();
        $totalSales = array_sum(array_column($finalResult, 'sum_of_net_sales'));

        // Add total row
        $finalResult['TOTAL'] = [
            'sum_of_net_sales' => $totalSales,
        ];

        return collect($finalResult);
    }

    public function getSalesPerQuarterByChannel()
    {
        $dataCollection = $this->getDataCollection();

        // Group by quarter and channel classification
        $salesSummary = $dataCollection->filter(function ($row) {
            return $row->sales_date >= "{$this->year}-01-01" && $row->sales_date <= "{$this->year}-{$this->previousMonth}-31";
        })->map(function ($row) {
                switch ($row->channel_code) {
                    case 'ONL':
                        $channelClassification = 'ECOMM';
                        break;
                    case 'DLR':
                    case 'CRP':
                    case 'OUT':
                        $channelClassification = 'DLR/CRP';
                        break;
                    case 'RTL':
                        $channelClassification = 'TOTAL-RTL';
                        break;
                    case 'FRA':
                        $channelClassification = 'FRA-DR';
                        break;
                    case 'SVC':
                        $channelClassification = 'SC';
                        break;
                    case 'CON':
                        $channelClassification = 'CON';
                        break;
                    default:
                        $channelClassification = 'OTHER';
                        break;
                }

            return [
                'quarter_cutoff' => 'Q' . ceil(date('n', strtotime($row->sales_date)) / 3),
                'channel_classification' => $channelClassification,
                'net_sales' => $row->net_sales,
                'reference_number' => $row->reference_number,
            ];
        })->groupBy(function ($item) {
            return "{$item['quarter_cutoff']}_{$item['channel_classification']}";
        })->map(function ($group) {
            return [
                'quarter_cutoff' => $group->first()['quarter_cutoff'],
                'channel_classification' => $group->first()['channel_classification'],
                'sum_of_net_sales' => $group->sum('net_sales'),
                'min_reference_number' => $group->min('reference_number'),
            ];
        });

        // Prepare final result with ROLLUP equivalent
        $finalResult = collect();
        foreach ($salesSummary as $key => $summary) {
            [$quarterCutoff, $channelClassification] = explode('_', $key);
            $finalResult->push(array_merge($summary, [
                'quarter_cutoff' => $quarterCutoff,
                'channel_classification' => $channelClassification,
            ]));
        }

        // Add totals for each classification
        $totalSummary = $finalResult->groupBy('channel_classification')->map(function ($group) {
            return [
                'sum_of_net_sales' => $group->sum('sum_of_net_sales'),
                'min_reference_number' => $group->min('min_reference_number'),
            ];
        })->toArray();

        // Append total summary to the final result
        foreach ($totalSummary as $classification => $totals) {
            $finalResult->push(array_merge($totals, [
                'quarter_cutoff' => 'TOTAL',
                'channel_classification' => $classification,
            ]));
        }

        return $finalResult->values(); // Reset keys and return
    }


    public function getYearToDate()
    {
    
        $dataCollection = $this->getDataCollection();

        // Group by category (APPLE vs NON-APPLE)
        $yearToDateSummary = $dataCollection->filter(function ($row) {
            return $row->sales_date >= "{$this->year}-01-01" && $row->sales_date <= "{$this->year}-{$this->previousMonth}-31";
        })->map(function ($row) {
            return [
                'category' => in_array($row->brand_description, ['APPLE', 'BEATS']) ? 'APPLE' : 'NON-APPLE',
                'net_sales' => $row->net_sales,
            ];
        })->groupBy('category')->map(function ($group) {
            return [
                'sum_of_net_sales' => $group->sum('net_sales'),
            ];
        });

        // Add total row
        $totalSales = $yearToDateSummary->sum('sum_of_net_sales');
        $yearToDateSummary['TOTAL'] = [
            'sum_of_net_sales' => $totalSales,
        ];

        return collect($yearToDateSummary);
    }

    public function getYearToDateWithSelection($channel = null, $concept = null)
    {


        $today = date('Y-m-d');

        $cacheKey = "store_sales_table_data_{$today}_{$this->year}";

        // Retrieve the cached data
        $storeSalesData = Cache::get($cacheKey);

        if (!$storeSalesData) {
            return []; 
        }
    
        // Convert cached data to a collection
        $dataCollection =  collect($storeSalesData);

        // Filter and group by category, channel, and concept
        $yearToDateSummary = $dataCollection->filter(function ($row) use($channel, $concept) {
     
            // Check the sales date range
            $dateCondition = $row->sales_date >= "{$this->year}-01-01" && $row->sales_date <= "{$this->year}-{$this->previousMonth}-31";
            
            // Determine channel condition
            $channelCondition = ($channel === null) || ($channel === 'all') || ($row->channels_id == (int)$channel);
            
            // Determine concept condition
            $conceptCondition = ($concept === null) || ($concept === 'all') || ($row->concepts_id == (int)$concept);

            // Combine all conditions
            return $dateCondition && $channelCondition && $conceptCondition;
        })->map(function ($row) {
            return [
                'category' => in_array($row->brand_description, ['APPLE', 'BEATS']) ? 'APPLE' : 'NON-APPLE',
                'channel_code' => $row->channel_code,
                'concept_name' => $row->concept_name,
                'net_sales' => $row->net_sales,
            ];
        })->groupBy('category')->map(function ($group) {
            return [
                'channel_code' => $group->min('channel_code'),
                'concept_name' => $group->min('concept_name'),
                'sum_of_net_sales' => $group->sum('net_sales'),
            ];
        });

        // Add channel and store concept
        $channelName = Channel::select('channel_name')->where('id', $channel)->value('channel_name');
        $conceptName = Concept::select('concept_name')->where('id',$concept)->value('concept_name');

        $yearToDateSummary['SELECTED'] = [
            'channel_name' => $channelName,
            'concept_name' => $conceptName,
        ];

        // Add total row
        $totalSales = $yearToDateSummary->sum('sum_of_net_sales');
        $yearToDateSummary['TOTAL'] = [
            'channel_code' => null,
            'concept_name' => null,
            'sum_of_net_sales' => $totalSales,
        ];

        return collect($yearToDateSummary);
    }
    

    private function getDataCollection(){

        $cacheKey = $this->getCacheKey();
    
        // Retrieve the cached data
        $storeSalesData = Cache::get($cacheKey);
    
        if (!$storeSalesData) {
            return []; 
        }
    
        // Convert cached data to a collection
        return collect($storeSalesData);
    }

    private function getCacheKey(){
        $today = date('Y-m-d');
        $cacheKey = "store_sales_table_data_{$today}_{$this->year}";
 
        return $cacheKey;
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

    // DATE_FORMAT(store_sales.sales_date, '%Y-%m') AS yearMonth,

    public static function generateChartData($params)
    {
        extract($params); 

        $params = [
            $yearFrom,
            $yearTo,
            $monthFrom,
            $monthTo,
            implode('|', (array)$stores),
            implode('|', (array)$concepts),
            implode('|', (array)$channels),
            implode('|', (array)$malls),
            implode('|', (array)$brands),
            implode('|', (array)$categories),
        ];

        \Log::info('start params');
        \Log::info($yearFrom);
        \Log::info($yearTo);
        \Log::info($monthFrom);
        \Log::info($monthTo);
        \Log::info($stores);
        \Log::info($concepts);
        \Log::info($channels);
        \Log::info($malls);
        \Log::info($brands);
        \Log::info($categories);
        \Log::info('end params');


        $cacheKey = 'chartData_' . md5(implode('|', $params));
        \Log::info($cacheKey);


        return Cache::remember($cacheKey, 50000, 
            function() use ($yearFrom, $yearTo, $monthFrom, $monthTo, $stores, $concepts, $channels, $malls, $brands, $categories) {
            $query = DB::table('store_sales', 'ss')
                ->select(
                    DB::raw("CONCAT('M', MONTH(sales_date)) AS month"),
                    DB::raw("CONCAT('Y', YEAR(sales_date)) AS year"),
                    DB::raw("SUM(net_sales) AS net_sales")
                )
                ->leftJoin('channels as ch', 'ss.channels_id', 'ch.id')
                ->leftJoin('customers as cu', 'ss.customers_id', 'cu.id')
                ->leftJoin('all_items as ai', 'ss.item_code', 'ai.item_code')
                ->leftJoin('concepts as con', 'cu.concepts_id', 'con.id')
                ->where('ss.is_final', 1)
                ->whereBetween(DB::raw('YEAR(ss.sales_date)'), [$yearFrom, $yearTo])
                ->whereBetween(DB::raw('MONTH(ss.sales_date)'), [$monthFrom, $monthTo])
                ->where('ss.quantity_sold', '>', 0)
                ->whereNotNull('ss.net_sales')
                ->where('ss.sold_price', '>', 0)
                ->where('ss.channels_id', '!=', 12);

                 // Conditional parameters
                if (!empty($stores)) {
                    $query->whereIn('cu.id', (array)$stores);
                }

                if (!empty($concepts)) {
                    $query->whereIn('con.id', (array)$concepts);
                }

                if (!empty($channels)) {
                    $query->whereIn('ch.id', (array)$channels);
                }

                if (!empty($malls)) {
                    $query->whereIn('cu.mall', $malls);
                }

                if (!empty($brands)) {
                    $query->whereIn('ai.brand_description', (array)$brands);
                }

                if (!empty($categories)) {
                    $query->whereIn('ai.category_description', (array)$categories);
                }

                $query->groupBy(DB::raw("year, month"));

                // For debugging: Log the raw SQL and parameters
                \Log::info(json_encode([
                    'query' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ], JSON_PRETTY_PRINT));

                return $query->get();
               
        });
    }
    public static function generateChartDataForMultipleChannel($params)
    {
        extract($params); 

        $params = [
            $yearFrom,
            $yearTo,
            $monthFrom,
            $monthTo,
            implode('|', (array)$stores),
            implode('|', (array)$concepts),
            implode('|', (array)$channels),
            implode('|', (array)$malls),
            implode('|', (array)$brands),
            implode('|', (array)$categories),
        ];

        \Log::info('multiple start params');
        \Log::info($yearFrom);
        \Log::info($yearTo);
        \Log::info($monthFrom);
        \Log::info($monthTo);
        \Log::info($stores);
        \Log::info($concepts);
        \Log::info($channels);
        \Log::info($malls);
        \Log::info($brands);
        \Log::info($categories);
        \Log::info('end params');


        $cacheKey = 'chartDataMultiple_' . md5(implode('|', $params));
        \Log::info($cacheKey);


        return Cache::remember($cacheKey, 50000, 
            function() use ($yearFrom, $yearTo, $monthFrom, $monthTo, $stores, $concepts, $channels, $malls, $brands, $categories) {
            $query = DB::table('store_sales', 'ss')
                ->select(
                    DB::raw("CONCAT('Y', YEAR(sales_date)) AS year"),
                    'ch.channel_code',
                    DB::raw("SUM(net_sales) AS net_sales")
                )
                ->leftJoin('channels as ch', 'ss.channels_id', 'ch.id')
                ->leftJoin('customers as cu', 'ss.customers_id', 'cu.id')
                ->leftJoin('all_items as ai', 'ss.item_code', 'ai.item_code')
                ->leftJoin('concepts as con', 'cu.concepts_id', 'con.id')
                ->where('ss.is_final', 1)
                ->whereBetween(DB::raw('YEAR(ss.sales_date)'), [$yearFrom, $yearTo])
                ->whereBetween(DB::raw('MONTH(ss.sales_date)'), [$monthFrom, $monthTo])
                ->where('ss.quantity_sold', '>', 0)
                ->whereNotNull('ss.net_sales')
                ->where('ss.sold_price', '>', 0)
                ->where('ss.channels_id', '!=', 12);

                 // Conditional parameters
                if (!empty($stores)) {
                    $query->whereIn('cu.id', (array)$stores);
                }

                if (!empty($concepts)) {
                    $query->whereIn('con.id', (array)$concepts);
                }

                if (!empty($channels)) {
                    $query->whereIn('ch.id', (array)$channels);
                }

                if (!empty($malls)) {
                    $query->whereIn('cu.mall', $malls);
                }

                if (!empty($brands)) {
                    $query->whereIn('ai.brand_description', (array)$brands);
                }

                if (!empty($categories)) {
                    $query->whereIn('ai.category_description', (array)$categories);
                }

                $query->groupBy(DB::raw("year, channel_code"));

                // For debugging: Log the raw SQL and parameters
                \Log::info(json_encode([
                    'query' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ], JSON_PRETTY_PRINT));

                return $query->get();
               
        });
    }




    public static function forTestData() {

       return Cache::remember('today12', 50000, function(){
            return DB::select(DB::raw("
            SELECT 
                CONCAT('M', MONTH(store_sales.sales_date)) AS month,
                CONCAT('Y', YEAR(store_sales.sales_date)) AS year,
    
        SUM(store_sales.net_sales) AS net_sales
    FROM store_sales 
    LEFT JOIN channels ON store_sales.channels_id = channels.id
    LEFT JOIN customers ON store_sales.customers_id = customers.id
    LEFT JOIN all_items ON store_sales.item_code = all_items.item_code
    LEFT JOIN concepts ON customers.concepts_id = concepts.id
    WHERE store_sales.is_final = 1 
        AND store_sales.sales_date BETWEEN '2022-01-01' AND '2024-09-30'
        AND store_sales.quantity_sold > 0
        AND store_sales.net_sales IS NOT NULL
        AND store_sales.sold_price > 0
        AND store_sales.channels_id != 12 
        AND customers.id = 807
        AND concepts.id = 324
        AND channels.id = 6
        AND customers.mall = 'CENTURY CITY'
        AND all_items.brand_description = 'APPLE'
        AND all_items.category_description = 'UNITS'
    GROUP BY year, month;

    "));
        });
        
    }
}
