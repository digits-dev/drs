<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RunRate extends Model
{
    use HasFactory;
    protected $table = 'run_rate';

    
    public function getByDate() {
        $sales_date = self::distinct('sales_date')->pluck('sales_date');
        $sum_queries = [];

        foreach ($sales_date as $date) {
            $sum_queries[] = DB::raw("SUM(CASE WHEN sales_date = '$date' THEN quantity_sold ELSE 0 END) as '$date'");
        }

        $store_sales = self::select(
            'digits_code_rr_ref',
            DB::raw("SUM(quantity_sold) as total_quantity_sold"),
            ...$sum_queries,
        )
        ->groupBy('digits_code_rr_ref');
        
        return $store_sales;
    }
}
