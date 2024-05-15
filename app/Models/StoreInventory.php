<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;
use Illuminate\Support\Facades\DB;

class StoreInventory extends Model
{
    use HasFactory;

    protected $table = 'store_inventories';

    protected $fillable = [
        'batch_date',
        'batch_number',
        'is_valid',
        'is_final',
        'reference_number',
        'systems_id',
        'organizations_id',
        'report_types_id',
        'channels_id',
        'inventory_transaction_types_id',
        'employees_id',
        'customers_id',
        'customer_location',
        'inventory_date',
        'item_code',
        'digits_code',
        'item_description',
        'quantity_inv',
        'srp',
        'qtyinv_srp',
        'store_cost',
        'qtyinv_sc',
        // 'current_srp',
        // 'qtyinv_csrp',
        'dtp_rf',
        'qtyinv_sc',
        'landed_cost',
        'qtyinv_lc',
        'dtp_ecom',
        'qtyinv_ecom',
        'created_by',
        'updated_by',
    ];

    public function scopeGetNextReference($query) {
        $storeInventory = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $storeInventory->reference_number + 1;
    }

    public function filterForReport($query, $filters = [], $is_upload = false) {
        $search = $filters['search'];
        if ($filters['datefrom'] && $filters['dateto']) {
            $query->whereBetween('store_inventories.inventory_date', [$filters['datefrom'], $filters['dateto']]);
        }
        if ($filters['channels_id']) {
            $query->where('store_inventories.channels_id', $filters['channels_id']);
        }
        if ($filters['systems_id']) {
            $query->where('store_inventories.systems_id', $filters['systems_id']);
        }
        if ($search)  {
            $search_filter = "
                store_inventories.reference_number like '%$search%' OR
                systems.system_name like '%$search%' OR
                report_types.report_type like '%$search%' OR
                channels.channel_name like '%$search%' OR
                customers.customer_name like '%$search%' OR
                concepts.concept_name like '%$search%' OR
                store_inventories.inventory_date LIKE '%$search%'            
            ";

            if ($is_upload) {
                $search_filter .= "
                    OR customers.customer_name LIKE '%$search%'
                    OR employees.employee_name LIKE '%$search%'
                    OR store_inventories.item_code LIKE '%$search%'
                    OR all_items.item_description LIKE '%$search%'
                    OR store_inventories.item_description LIKE '%$search%'
                    OR all_items.margin_category_description LIKE '%$search%'
                    OR all_items.brand_description LIKE '%$search%'
                    OR all_items.sku_status_description LIKE '%$search%'
                    OR all_items.category_description LIKE '%$search%'
                    OR all_items.margin_category_description LIKE '%$search%'
                    OR all_items.vendor_type_code LIKE '%$search%'
                    OR all_items.inventory_type_description LIKE '%$search%'
                ";
            }
            $query->whereRaw("($search_filter)");
        }
        return $query;
    }

    public function generateReport($ids = null) {
        $query = StoreInventory::select(
            'store_inventories.id',
            'store_inventories.batch_number',
            'store_inventories.is_final',
            'store_inventories.reference_number',
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            'inventory_transaction_types.inventory_transaction_type',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            'concepts.concept_name AS store_concept_name',
            'concepts.concept_name AS store_concept_name',
            'store_inventories.inventory_date AS inventory_date',
            'store_inventories.item_code AS item_code',
            'store_inventories.item_description AS item_description',
            'all_items.item_code AS digits_code',
            DB::raw('COALESCE(all_items.item_description, store_inventories.item_description) AS imfs_item_description'),
            'all_items.upc_code AS upc_code',
            'all_items.upc_code2 AS upc_code2',
            'all_items.upc_code3 AS upc_code3',
            'all_items.upc_code4 AS upc_code4',
            'all_items.upc_code5 AS upc_code5',
            'all_items.brand_description AS brand_description',
            'all_items.category_description AS category_description',
            'all_items.margin_category_description AS margin_category_description',
            'all_items.vendor_type_code AS vendor_type_code',
            'all_items.inventory_type_description AS inventory_type_description',
            'all_items.sku_status_description AS sku_status_description',
            'all_items.brand_status AS brand_status',
            'store_inventories.quantity_inv AS quantity_inv',
            'all_items.current_srp AS current_srp',
            DB::raw('(
                all_items.current_srp *store_inventories.quantity_inv
             ) AS qtyinv_srp'),
            'store_inventories.store_cost AS store_cost',
            'store_inventories.qtyinv_sc AS qtyinv_sc',
            'store_inventories.dtp_rf AS dtp_rf',
            'store_inventories.qtyinv_rf AS qtyinv_rf',
            'store_inventories.landed_cost AS landed_cost',
            'store_inventories.qtyinv_lc AS qtyinv_lc',
            'store_inventories.dtp_ecom AS dtp_ecom',
            'store_inventories.qtyinv_ecom as qtyinv_ecom',
        )
        ->leftJoin('systems', 'store_inventories.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'store_inventories.organizations_id', '=', 'organizations.id')
        ->leftJoin('inventory_transaction_types', 'inventory_transaction_types.id', '=','store_inventories.inventory_transaction_types_id')
        ->leftJoin('report_types', 'store_inventories.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'store_inventories.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'store_inventories.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'store_inventories.employees_id', '=', 'employees.id')
        ->leftJoin('all_items', 'store_inventories.item_code', '=', 'all_items.item_code');

        if (isset($ids)) {
            $query->whereIn('store_inventories.id', $ids);
        }
        return $query;
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            if (CRUDBooster::myId()) {
                $model->created_by = CRUDBooster::myId();
            }
        });
        static::updating(function($model)
        {
            if (CRUDBooster::myId()) {
                $model->updated_by = CRUDBooster::myId();
            }
        });
    }
}