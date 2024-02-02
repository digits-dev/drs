<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RunRate extends Model
{
    use HasFactory;
    protected $table = 'run_rate';

    
    public function filterRunRate($filters, $cutoff_queries = [], $search = null) {
        $query = self::whereNotNull('digits_code_rr_ref');

        foreach ($filters as $filter) {
            $query->where(...$filter);
        }
        if ($search) {
            $query->where('digits_code_rr_ref', 'like', "%$search%");
        }
        $query
            ->select('digits_code_rr_ref', ...$cutoff_queries)
            ->groupBy('digits_code_rr_ref');

        return $query;
        
    }
}
