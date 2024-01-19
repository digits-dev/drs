<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitsSalesReport extends Model
{
    use HasFactory;

    protected $table = 'digits_sales_report';

    public function scopeFilter($query, array $filters) {
        if($filters['search'] ?? false) {
            $table = $this->getTable();
            $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($table);
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%' . $filters['search'] . '%');
            }
        }
    }

    public function scopeSearchFilter($query, array $filters) {
        foreach($filters as $key => $value) {
            if (in_array($key, $this->fillable)){
                if (!empty($value)) {
                    $query->where($key, $value);
                }
            }
        }
        if (isset($filters['datefrom']) && isset($filters['dateto'])) {
            $query->whereBetween('sales_date', [$filters['datefrom'], $filters['dateto']]);
        }

        $query->where('is_final', 1);
        return $query;
    }
}
