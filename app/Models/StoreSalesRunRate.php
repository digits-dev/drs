<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StoreSalesRunRate extends Model
{
    use HasFactory;
    protected $table ='store_sales_run_rate';
    public function filterRunRate($params) {
        $filters = $params['filters'];
        $cutoff_queries = $params['cutoff_queries'];
        $search = $params['search'];
        $column_name = $params['column_name'];
        $last_12 = $params['last_12'];
        $query = self::whereNotNull('digits_code_rr_ref')->whereIn($column_name, $last_12);

        foreach ($filters as $filter) {
            $query->{$filter['method']}(...$filter['params']);
        }
        if ($search) {
            $query->where('digits_code_rr_ref', 'like', "%$search%");
        }
        $query
            ->select('digits_code_rr_ref', 'initial_wrr_date', ...$cutoff_queries)
            ->groupBy('digits_code_rr_ref', 'initial_wrr_date')
            ->orderBy('digits_code_rr_ref', 'ASC');

        return $query;
        
    }

    public function sumByCutOff($params) {
        $filters = $params['filters'];
        $search = $params['search'];
        $column_name = $params['column_name'];
        $last_12 = $params['last_12'];
        $query = self::whereNotNull('digits_code_rr_ref');

        foreach ($filters as $filter) {
            $query->{$filter['method']}(...$filter['params']);
        }
        if ($search) {
            $query->where('digits_code_rr_ref', 'like', "%$search%");
        }

        return $query
            ->whereIn($column_name, $last_12)
            ->select($column_name, DB::raw("SUM(quantity_sold) AS total"))
            ->groupBy($column_name)
            ->orderBy($column_name, 'DESC');
    }
}
