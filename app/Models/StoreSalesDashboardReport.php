<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSalesDashboardReport extends Model
{
    use HasFactory;

    protected $table = 'temp_store_sales';

    public $timestamps = false;

    private $year;
    private $month;

    public function __construct($year, $month){
        $this->year = $year;
        $this->month = $month;
    }

    public static function createTempTable($year, $month){

        // Normalize the month to always be two digits
    $month = str_pad($month, 2, '0', STR_PAD_LEFT);
    // Define the first day of the month
    $startDate = "{$year}-{$month}-01";

    // Create a DateTime object for the first day of the next month
    // $nextMonth = str_pad(($month % 12) + 1, 2, '0', STR_PAD_LEFT);
    // $nextMonthStart = "{$year}-{$nextMonth}-01";
    
    // Calculate the end date for the current month
    $endDate = date("Y-m-d", strtotime("last day of {$startDate}"));
    // dump($endDate);

    DB::statement("
        CREATE TEMPORARY TABLE temp_store_sales AS
        SELECT 
            store_sales.*,
            non_apple_cutoffs.week_cy,
            non_apple_cutoffs.from_date,
            non_apple_cutoffs.sold_date,
            channels.channel_code
        FROM store_sales 
        LEFT JOIN non_apple_cutoffs ON store_sales.sales_date = non_apple_cutoffs.sold_date
        LEFT JOIN channels ON store_sales.channels_id = channels.id
        WHERE store_sales.is_final = 1 
            AND non_apple_cutoffs.sold_date BETWEEN '{$startDate}' AND '{$endDate}'
            AND store_sales.quantity_sold > 0
            AND store_sales.net_sales IS NOT NULL
            AND store_sales.sold_price > 0
    ");
    // AND non_apple_cutoffs.week_cy IN ('WK01', 'WK02', 'WK03', 'WK04')

    }

    public static function dropTempTable(){
        DB::statement('DROP TEMPORARY TABLE IF EXISTS temp_store_sales');
    }

    public static function getSalesSummary()
    {
     
        return self::select(
                'week_cy',
                DB::raw('SUM(net_sales) AS sum_of_net_sales')
            )->groupBy('week_cy')
            ->get();

    }

    public static function getSalesWeeklyPerChannel()
    {
        return self::select(
                'week_cy',
                DB::raw('SUM(net_sales) AS sum_of_net_sales'),
                'channel_code',
            )
            ->groupBy('week_cy', 'channel_code')
            ->get();
    }

    public static function getLastThreeDaysOfMonth($year, $month)
    {
        // Create a DateTime object for the first day of the next month
        $date = new \DateTime("{$year}-{$month}-01");
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

    public static function getLastThreeDaysWithSales($year, $month)
    {
        // Fetch distinct sales dates with sales data for the given month
        $salesDates = self::select('sales_date')
            ->whereYear('sales_date', $year)
            ->whereMonth('sales_date', $month)
            ->distinct()
            ->orderBy('sales_date', 'desc')
            ->limit(3)
            ->pluck('sales_date')
            ->toArray();

        // Return the last three sales dates
        return $salesDates;
    }

    public static function getSalesSummaryForLastThreeDays($year, $month)
    {
        $lastThreeDays = self::getLastThreeDaysOfMonth($year, $month);

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

    public static function getSalesSummaryForLastThreeDaysPerChannel($year, $month)
    {

        $lastThreeDays = self::getLastThreeDaysOfMonth($year, $month);

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
