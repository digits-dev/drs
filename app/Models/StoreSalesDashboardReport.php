<?php

namespace App\Models;

use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;

class StoreSalesDashboardReport extends Model
{
    use HasFactory;

    protected $table = 'temp_store_sales';

    public $timestamps = false;

    protected $fillable = ['year', 'month', 'day'];

    protected $year;
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
            $this->month = str_pad($attributes['month'], 2, '0', STR_PAD_LEFT); // always two digits
            // $this->day = str_pad($attributes['day'], 2, '0', STR_PAD_LEFT); // always two digits
            $this->day = $attributes['day']; // always two digits

            $this->yearMonth = "{$this->year}-{$this->month}";
            $this->currentDayAsDate = "{$this->year}-{$this->month}-{$this->day}";
            $this->startDate = "{$this->year}-{$this->month}-01";
            $this->endDate = date("Y-m-d", strtotime("last day of {$this->startDate}"));
        }

        Log::info("Month $this->month");
        Log::info("Year $this->year");
        Log::info("Day $this->day");
        Log::info("YearMonth $this->yearMonth");
        Log::info("StartDate $this->startDate");
        Log::info("EndDate $this->endDate");

    }

    public function createTempTable(){

    DB::statement("
        CREATE TEMPORARY TABLE temp_store_sales AS
        SELECT 
            store_sales.sales_date,
            store_sales.net_sales,
            store_sales.reference_number,
            channels.channel_code
        FROM store_sales 
        LEFT JOIN channels ON store_sales.channels_id = channels.id
        WHERE store_sales.is_final = 1 
            AND YEAR(store_sales.sales_date) = {$this->year}
            AND store_sales.quantity_sold > 0
            AND store_sales.net_sales IS NOT NULL
            AND store_sales.sold_price > 0
            AND store_sales.channels_id != 12 
    ");
    // AND store_sales.sales_date BETWEEN '{$this->startDate}' AND '{$this->endDate}'

    // AND store_sales.channels_id != 12  = EEE is not included 

    }

    public function dropTempTable(){
        DB::statement('DROP TEMPORARY TABLE IF EXISTS temp_store_sales');
    }

    public function getSalesSummary()
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

    public function getSalesWeeklyPerChannel()
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


    public function getSalesSummaryForLastThreeDays()
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

    public function getSalesSummaryForLastThreeDaysPerChannel()
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

    //Monthly Sales Report

    public function createTempTableForMonthly(){

        DB::statement("
            CREATE TEMPORARY TABLE monthly_temp_store_sales AS
            SELECT 
                store_sales.sales_date,
                store_sales.net_sales,
                store_sales.reference_number,
                channels.channel_code
            FROM store_sales 
            LEFT JOIN channels ON store_sales.channels_id = channels.id
            WHERE store_sales.is_final = 1 
                AND YEAR(store_sales.sales_date) = {$this->year}
                AND store_sales.quantity_sold > 0
                AND store_sales.net_sales IS NOT NULL
                AND store_sales.sold_price > 0
                AND store_sales.channels_id != 12 
        ");
    
        // AND store_sales.channels_id != 12  = EEE is not included 
    
        }
    
        public function dropTempTableForMonthly(){
            DB::statement('DROP TEMPORARY TABLE IF EXISTS monthly_temp_store_sales');
        }

        public function getSalesPerMonth()
        {
            return DB::table('temp_store_sales')->select(DB::raw("
                CONCAT('M', LPAD(MONTH(sales_date), 2, '0')) AS month_cutoff,
                SUM(net_sales) AS sum_of_net_sales
            "))
            ->groupBy(DB::raw("month_cutoff WITH ROLLUP"))
            ->get()->map(function($item) {
                if(is_null($item->month_cutoff)){
                    $item->month_cutoff = 'TOTAL';
                }
                return (array) $item;
            })->keyBy('month_cutoff');
        }


        public function getSalesPerMonthByChannel()
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
            ->groupBy('channel_classification', DB::raw('month_cutoff WITH ROLLUP'))
            ->orderByRaw("FIELD(channel_classification, 'ECOMM', 'TOTAL-RTL', 'SC', 'DLR/CRP', 'CON', 'FRA-DR', 'OTHER')")
            ->get()->map(function($item){
                if(is_null($item->month_cutoff)){
                    $item->month_cutoff = 'TOTAL';
                }
                return (array) $item;
            });
        }


        public function getSalesPerQuarter()
        {
            return DB::table('temp_store_sales')->select(DB::raw("
                CONCAT('Q', QUARTER(sales_date)) AS quarter_cutoff,
                SUM(net_sales) AS sum_of_net_sales
            "))
            ->groupBy(DB::raw("quarter_cutoff WITH ROLLUP"))
            ->get()->map(function($item) {
                if (is_null($item->quarter_cutoff)) {
                    $item->quarter_cutoff = 'TOTAL';
                }
                return (array) $item;
            })->keyBy('quarter_cutoff');
        }

        public function getSalesPerQuarterByChannel()
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
            ->groupBy('channel_classification', DB::raw('quarter_cutoff WITH ROLLUP'))
            ->orderByRaw("FIELD(channel_classification, 'ECOMM', 'TOTAL-RTL', 'SC', 'DLR/CRP', 'CON', 'FRA-DR', 'OTHER')")
            ->get()->map(function($item){
                if(is_null($item->quarter_cutoff)){
                    $item->quarter_cutoff = 'TOTAL';
                }
                return (array) $item;
            });
        }


     


}
