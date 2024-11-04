<?php

namespace App\Models;

use App\Helpers\QueryLogger;
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
                $currentYear = date("Y");
                // $currentYear = 2022;

                /* Logic:
                    If today is January of the current year and the current iteration year matches,
                    then the month is January; otherwise, it is December.
                    This is used for scoping data in the getStoreSales method.
                */
                
                if($currentYear == $attributes['year']){
                    //Use For Daily & Weekly Sales Report
                    $this->month = $month;
                } else {
                    //Use For Other Reports, Monthly, Quarterly, YTD  
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

        // Log::debug("Year $this->year");
        // Log::debug("RAW Month $this->rawMonth");
        // Log::debug("Previous Month $this->previousMonth");
        // Log::debug("Month $this->month");
        // Log::debug("Day $this->day");

        // Log::debug("Current Day as Date $this->currentDayAsDate");
        // Log::debug("YearMonth $this->yearMonth");
        // Log::debug("StartDate $this->startDate");
        // Log::debug("EndDate $this->endDate");

    }

    //FIRST APPROACH - POSTFIX WITH "BackUp" - NOT BEING USE RIGHT NOW
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


     //SECOND APPROACH - Utilizes Laravel collection methods to retrieve the appropriate data.

     public function getStoreSalesData() {

        $cacheKey = $this->getCacheKey();

        Cache::forget($cacheKey); 

        // Cache the results of the query
        $storeSalesData = Cache::remember($cacheKey, now()->endOfDay(), function() {
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

        // channels id "12" = EEE or Employee channel 
    
        return $storeSalesData;
    }

    public function getSalesDataFrom($salesTable = 'store_sales') {
        $allowedTables = ['store_sales', 'digits_sales']; 

        if (!in_array($salesTable, $allowedTables)) {
            throw new \InvalidArgumentException("Invalid table name.");
        }
    
        $cacheKey = $this->getCacheKey();
    
        // Clear previous cache
        Cache::forget($cacheKey);
    
        // Cache the results of the query
        $storeSalesData = Cache::remember($cacheKey, now()->endOfDay(), function() use ($salesTable) {
            return DB::table($salesTable)
                ->select([
                    "{$salesTable}.sales_date",
                    "{$salesTable}.net_sales",
                    "{$salesTable}.reference_number",
                    'channels.channel_code',
                    "{$salesTable}.channels_id",
                    'customers.concepts_id',
                    'all_items.brand_description',
                    'concepts.concept_name'
                ])
                ->leftJoin('channels', "{$salesTable}.channels_id", '=', 'channels.id')
                ->leftJoin('customers', "{$salesTable}.customers_id", '=', 'customers.id')
                ->leftJoin('all_items', "{$salesTable}.item_code", '=', 'all_items.item_code')
                ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
                ->where("{$salesTable}.is_final", 1)
                ->whereYear("{$salesTable}.sales_date", $this->year)
                ->whereBetween(DB::raw('MONTH(' . "{$salesTable}.sales_date" . ')'), [1, $this->month])
                ->where("{$salesTable}.quantity_sold", '>', 0)
                ->whereNotNull("{$salesTable}.net_sales")
                ->where("{$salesTable}.sold_price", '>', 0)
                ->where("{$salesTable}.channels_id", '!=', 12)
                ->get();
        });
    
        // channels id "12" = EEE or Employee channel 
    
        return $storeSalesData;
    }

    public function getSalesSummary()
    {

        $lastDay = $this->getLastDay();
        $dataCollection = $this->getDataCollection();

        // Group the data by week cutoff
        $salesSummary = $dataCollection->filter(function($row) use($lastDay) {

            return $this->filterSalesByDateRange($row->sales_date, $lastDay);

        })->map(function($row) {
            // Determine week cutoff
            $weekCutoff = $this->getWeekCutOff($row->sales_date);

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

        $lastDay = $this->getLastDay();
        $dataCollection = $this->getDataCollection();

        // Group the data by week and channel classification
        $salesSummary = $dataCollection->filter(function($row) use($lastDay) {

            return $this->filterSalesByDateRange($row->sales_date, $lastDay);

        })->map(function($row) {
            // Determine week cutoff
            $weekCutoff = $this->getWeekCutOff($row->sales_date);

            // Determine channel classification
            $channelClassification = $this->getSwitchChannel($row->channel_code);

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
               
            // Determine channel classification
            $channelClassification = $this->getSwitchChannel($row->channel_code);

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
            // Determine channel classification
            $channelClassification = $this->getSwitchChannel($row->channel_code);

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
            // Determine channel classification
            $channelClassification = $this->getSwitchChannel($row->channel_code);

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

    // private function getCacheKey(){
    //     $today = date('Y-m-d');
    //     $cacheKey = "store_sales_table_data_{$today}_{$this->year}";
 
    //     return $cacheKey;
    // }

    private function getCacheKey($salesTable = 'store_sales') {
        $today = Carbon::today()->toDateString(); 
        $cacheKey = "{$salesTable}_table_data_{$today}_{$this->year}";
    
        return $cacheKey;
    }

    public static function generateChartData($params)
    {
        return self::generateChartDataByParams($params, 'chartData_');
    }

    public static function generateChartDataForMultipleChannel($params)
    {
        return self::generateChartDataByParams($params, 'chartDataMultiple_', true);
    }

    private function getLastDay(){
        $lastThreeDays = $this->getLastThreeDaysDates($this->currentDayAsDate);
        $lastDay = end($lastThreeDays);

        return $lastDay;
    }

    private function filterSalesByDateRange($sales_date, $lastDay){
        if($lastDay <= $this->endDate) {
            return $sales_date >= $this->startDate && $sales_date <= $lastDay;
        } else {
            return $sales_date >= $this->startDate && $sales_date <= $this->endDate;
        }
    }

    private function getWeekCutOff($sales_date){
        $weekCutoff = null;

        if ($sales_date >= $this->startDate && $sales_date <= "{$this->yearMonth}-07") {
            $weekCutoff = 'WK01';
        } elseif ($sales_date >= "{$this->yearMonth}-08" && $sales_date <= "{$this->yearMonth}-14") {
            $weekCutoff = 'WK02';
        } elseif ($sales_date >= "{$this->yearMonth}-15" && $sales_date <= "{$this->yearMonth}-21") {
            $weekCutoff = 'WK03';
        } elseif ($sales_date >= "{$this->yearMonth}-22" && $sales_date <= $this->endDate) {
            $weekCutoff = 'WK04';
        }

        return $weekCutoff;
    }

    private function getSwitchChannel($channelCode){
        $channelClassification = null;

        switch ($channelCode) {
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

        return $channelClassification;
    }

    private static function generateChartDataByParams($params, $cachePrefix, $includeChannelCode = false)
    {
        \Log::debug("Params For $cachePrefix");
        foreach ($params as $key => $value) {
            // Check if the value is an array
            if (is_array($value)) {
                // Log the array values
                \Log::debug("$key => " . json_encode($value));
            } else {
                // Log the simple value
                \Log::debug("$key => $value");
            }
        }

        extract($params);

        // Prepare parameters for caching
        $paramsForKey = [
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

        $cacheKey = $cachePrefix . md5(implode('|', $paramsForKey));
        // \Log::debug($cacheKey);

        return Cache::remember($cacheKey, now()->endOfDay(), function() use ($yearFrom, $yearTo, $monthFrom, $monthTo, $stores, $concepts, $channels, $malls, $brands, $categories, $includeChannelCode) {
            return self::buildChartQuery($yearFrom, $yearTo, $monthFrom, $monthTo, $stores, $concepts, $channels, $malls, $brands, $categories, $includeChannelCode)
                ->get();
        });
    }

    private static function buildChartQuery($yearFrom, $yearTo, $monthFrom, $monthTo, $stores, $concepts, $channels, $malls, $brands, $categories, $includeChannelCode)
    {
        $query = DB::table('store_sales', 'ss')
            ->select(
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

        // Apply conditional parameters
        self::applyConditionalFilters($query, $stores, $concepts, $channels, $malls, $brands, $categories);

        if ($includeChannelCode) {
            $query->addSelect('ch.channel_code');
            $query->groupBy(DB::raw("year, ch.channel_code")); // Group by channel code if included
        } else {
            $query->addSelect( DB::raw("CONCAT('M', MONTH(sales_date)) AS month"));
            $query->groupBy(DB::raw("year, month")); // Only group by year and month sif not
        }

        QueryLogger::logQuery($query);

        return $query;
    }

    private static function applyConditionalFilters($query, $stores, $concepts, $channels, $malls, $brands, $categories)
    {
        if (!empty($stores) && $stores[0] !== 'all') {
            $query->whereIn('cu.id', (array)$stores);
        }

        if (!empty($concepts) && $concepts[0] !== 'all') {
            $query->whereIn('con.id', (array)$concepts);
        }

        if (!empty($channels) && $channels[0] !== 'all') {
            $query->whereIn('ch.id', (array)$channels);
        }

        if (!empty($malls) && $malls[0] !== 'all') {
            $query->whereIn('cu.mall', $malls);
        }

        if (!empty($brands) && $brands[0] !== 'all') {
            $query->whereIn('ai.brand_description', (array)$brands);
        }

        if (!empty($categories) && $categories[0] !== 'all') {
            $query->whereIn('ai.category_description', (array)$categories);
        }
    }
}
