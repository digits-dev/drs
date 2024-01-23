<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoriesReport extends Model
{
    use HasFactory;
    protected $table = 'warehouse_inventories_report';
    protected $guarded = [];

    public function scopeFilter($query, array $filters) {
        if($filters['search'] ?? false) {
            $table = $this->getTable();
            $columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($table);
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%' . $filters['search'] . '%');
            }
        }
    }
}
