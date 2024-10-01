<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;

class StoreSalesDashboardReport extends Model
{
    use HasFactory;

    protected $table = 'temp_store_sales';

    public $timestamps = false;

    protected $fillable = ['year', 'month'];

    protected $year;
    protected $month;
    protected $yearMonth;
    protected $startDate;
    protected $endDate;

    public function __construct(array $attributes = []){
        parent::__construct($attributes);
        
        // Initialize year and month, and set properties
        if (isset($attributes['year']) && isset($attributes['month'])) {
            $this->year = $attributes['year'];
            $this->month = str_pad($attributes['month'], 2, '0', STR_PAD_LEFT); // always two digits

            $this->yearMonth = "{$this->year}-{$this->month}";
            $this->startDate = "{$this->year}-{$this->month}-01";
            $this->endDate = date("Y-m-d", strtotime("last day of {$this->startDate}"));
        }

        Log::info("Month $this->month");
        Log::info("Year $this->year");
        Log::info("YearMonth $this->yearMonth");
        Log::info("StartDate $this->startDate");
        Log::info("EndDate $this->endDate");

    }

    public function createTempTable(){

    DB::statement("
        CREATE TEMPORARY TABLE temp_store_sales AS
        SELECT 
            store_sales.*,
            channels.channel_code
        FROM store_sales 
        LEFT JOIN channels ON store_sales.channels_id = channels.id
        WHERE store_sales.is_final = 1 
            AND store_sales.sales_date BETWEEN '{$this->startDate}' AND '{$this->endDate}'
            AND store_sales.quantity_sold > 0
            AND store_sales.net_sales IS NOT NULL
            AND store_sales.sold_price > 0
    ");

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
        ->groupBy('week_cutoff')
        ->get();

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
            SUM(net_sales) AS sum_of_net_sales,
            channel_code
        "))
        ->groupBy('week_cutoff', 'channel_code')
        ->get();
    }

    public function getLastThreeDaysOfMonth()
    {
        // Create a DateTime object for the first day of the next month
        $date = new \DateTime("{$this->yearMonth}-01");
        // Modify the date to the last day of the current month
        $date->modify('last day of this month');
        
        // Get the last three days
        $lastThreeDays = [];
        for ($i = 2; $i >= 0; $i--) {
            $lastThreeDays[] = $date->format('Y-m-d');
            $date->modify('-1 day');
        }

        return $lastThreeDays;
    }

    public function getLastThreeDaysWithSales()
    {
        // Fetch distinct sales dates with sales data for the given month
        $salesDates = self::select('sales_date')
            ->whereYear('sales_date', $this->year)
            ->whereMonth('sales_date', $this->month)
            ->distinct()
            ->orderBy('sales_date', 'desc')
            ->limit(3)
            ->pluck('sales_date')
            ->toArray();

        // Return the last three sales dates
        return $salesDates;
    }

    public function getSalesSummaryForLastThreeDays()
    {
        $lastThreeDays = self::getLastThreeDaysWithSales();

        // Fetch the sales summary for those days
        return self::select(
            DB::raw("DATE_FORMAT(sales_date, '%d-%b') AS date_of_the_day"),
                DB::raw('SUM(net_sales) AS sum_of_net_sales')
            )
            ->whereIn('sales_date', $lastThreeDays) // Filter by the last three days
            ->groupBy('sales_date') // Group by sales_date
            ->get()->map(function ($item) {
                $item->day = date('D', strtotime($item->date_of_the_day));
                return $item;
            });;
    }

    public function getSalesSummaryForLastThreeDaysPerChannel()
    {

        $lastThreeDays = self::getLastThreeDaysWithSales();

        // Fetch the sales summary for those days
        return self::select(
                DB::raw("DATE_FORMAT(sales_date, '%d-%b') AS date_of_the_day"),
                DB::raw('SUM(net_sales) AS sum_of_net_sales'),
                'channel_code'
            )
            ->whereIn('sales_date', $lastThreeDays) // Filter by the last three days
            ->groupBy('sales_date', 'channel_code') // Group by sales_date
            ->get()->map(function ($item) {
                $item->day = date('D', strtotime($item->date_of_the_day));
                return $item;
            });;
    }


}
