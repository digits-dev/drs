<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventoriesReport extends Model
{
    use HasFactory;
    protected $table = 'store_inventories_report';
    protected $guarded = [];
    protected $fillable = [
        "reference_number",
        "system_name",
        "organization_name",
        "report_type",
        "channel_name",
        "customer_location",
        "store_concept_name",
        "item_code",
        "item_description",
        "quantity_inv",
        "digits_code",
        "upc_code",
        "upc_code2",
        "upc_code3",
        "upc_code4",
        "upc_code5",
        "imfs_item_description",
        "brand_description",
        "category_description",
        "margin_category_description",
        "vendor_type_code",
        "inventory_type_description",
        "sku_status_description",
        "brand_status",
        "landed_cost",
    ];

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
            $query->whereBetween('inventory_date', [$filters['datefrom'], $filters['dateto']]);
        }

        $query->where('is_final', 1);
        return $query;
    }
}