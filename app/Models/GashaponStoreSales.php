<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;
use Illuminate\Support\Facades\DB;

class GashaponStoreSales extends Model
{
    use HasFactory;

    protected $table = 'gashapon_store_sales';

    protected $fillable = [
        'batch_date',
        'batch_number',
        'is_valid',
        'is_final',
        'rr_flag',
        'rr_flag_temp',
        'reference_number',
        'systems_id',
        'organizations_id',
        'report_types_id',
        'channels_id',
        'sales_transaction_types_id',
        'employees_id',
        'customers_id',
        'customer_location',
        'receipt_number',
        'sales_date',
        'apple_yr_qtr_wk',
        'apple_week_cutoff',
        'non_apple_yr_mon_wk',
        'non_apple_week_cutoff',
        'sales_date_yr_mo',
        'sales_year',
        'sales_month',
        'item_code',
        'digits_code_rr_ref',
        'digits_code',
        'item_description',
        'quantity_sold',
        'sold_price',
        'qtysold_price',
        'store_cost',
        'qtysold_sc',
        'net_sales',
        'sale_memo_reference',
        'current_srp',
        'qtysold_csrp',
        'dtp_rf',
        'qtysold_sc',
        'landed_cost',
        'qtysold_lc',
        'dtp_ecom',
        'qtysold_ecom',
        'created_by',
        'updated_by',
    ];

    public function scopeGetNextReference($query) {
        $gashaponStoreSales = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $gashaponStoreSales->reference_number + 1;
    }

    public function filterForReport($query, $filters = [], $is_upload = false) {
        $search = $filters['search'];
        
        if ($filters['datefrom'] && $filters['dateto']) {
            $query->whereBetween('gashapon_store_sales.sales_date', [$filters['datefrom'], $filters['dateto']]);
        }
    
        if ($filters['channels_id']) {
            $query->where('gashapon_store_sales.channels_id', $filters['channels_id']);
        }
        if ($filters['concepts_id']) {
            $query->where('customers.concepts_id', $filters['concepts_id']);
        }
        if ($filters['receipt_number']) {
            $query->where('gashapon_store_sales.receipt_number', $filters['receipt_number']);
        }
        // if (isset($search))  {
        //     $search_filter = "
        //         gashapon_store_sales.reference_number LIKE '%$search%' OR
        //         systems.system_name LIKE '%$search%' OR
        //         report_types.report_type LIKE '%$search%' OR
        //         channels.channel_name LIKE '%$search%' OR
        //         customers.customer_name LIKE '%$search%' OR
        //         concepts.concept_name LIKE '%$search%' OR
        //         gashapon_store_sales.receipt_number LIKE '%$search%' OR
        //         gashapon_store_sales.sales_date LIKE '%$search%'
        //     ";
        //     if ($is_upload) {
        //         $search_filter .= "
        //             OR customers.customer_name LIKE '%$search%'
        //             OR employees.employee_name LIKE '%$search%'
        //             OR gashapon_store_sales.item_code LIKE '%$search%'
        //             OR all_items.item_description LIKE '%$search%'
        //             OR gashapon_store_sales.item_description LIKE '%$search%'
        //             OR all_items.margin_category_description LIKE '%$search%'
        //             OR all_items.brand_description LIKE '%$search%'
        //             OR all_items.sku_status_description LIKE '%$search%'
        //             OR all_items.category_description LIKE '%$search%'
        //             OR all_items.margin_category_description LIKE '%$search%'
        //             OR all_items.vendor_type_code LIKE '%$search%'
        //             OR all_items.inventory_type_description LIKE '%$search%'";
        //     }
        //     $query->whereRaw("($search_filter)");
        // }
       
        return $query;
    }

    public function generateReport($ids = null) {
        $query = GashaponStoreSales::select(
            'gashapon_store_sales.id',
            'gashapon_store_sales.batch_number',
            'gashapon_store_sales.is_final',
            'gashapon_store_sales.reference_number',
            DB::raw('all_items.digits_code IS NOT NULL AS rr_flag'),
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            'concepts.concept_name AS concept_name',
            'gashapon_store_sales.receipt_number AS receipt_number',
            'gashapon_store_sales.sales_date AS sales_date',
            'apple_cutoffs.apple_yr_qtr_wk AS apple_yr_qtr_wk',
            'apple_cutoffs.apple_week_cutoff AS apple_week_cutoff',
            'non_apple_cutoffs.non_apple_yr_mon_wk AS non_apple_yr_mon_wk',
            'non_apple_cutoffs.non_apple_week_cutoff AS non_apple_week_cutoff',
            DB::raw('DATE_FORMAT(gashapon_store_sales.sales_date, "%Y_%m") AS sales_date_yr_mo'),
            DB::raw('DATE_FORMAT(gashapon_store_sales.sales_date, "%Y") AS sales_year'),
            DB::raw('DATE_FORMAT(gashapon_store_sales.sales_date, "%m") AS sales_month'),
            'gashapon_store_sales.item_code AS item_code',
            'gashapon_store_sales.item_description AS item_description',
            DB::raw('COALESCE(gashapon_store_sales.digits_code_rr_ref, gashapon_store_sales.item_code) AS digits_code_rr_ref'),
            'all_items.item_code AS digits_code',
            DB::raw('COALESCE(all_items.item_description, gashapon_store_sales.item_description) AS imfs_item_description'),
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
            'gashapon_store_sales.quantity_sold AS quantity_sold',
            'gashapon_store_sales.sold_price AS sold_price',
            'gashapon_store_sales.qtysold_price AS qtysold_price',
            'gashapon_store_sales.store_cost AS store_cost',
            'gashapon_store_sales.qtysold_sc AS qtysold_sc',
            'gashapon_store_sales.net_sales AS net_sales',
            'gashapon_store_sales.sale_memo_reference AS sale_memo_reference',
            'gashapon_store_sales.current_srp AS current_srp',
            'gashapon_store_sales.qtysold_csrp AS qtysold_csrp',
            'gashapon_store_sales.dtp_rf AS dtp_rf',
            'gashapon_store_sales.qtysold_rf AS qtysold_rf',
            'gashapon_store_sales.landed_cost AS landed_cost',
            'gashapon_store_sales.qtysold_lc AS qtysold_lc',
            'gashapon_store_sales.dtp_ecom AS dtp_ecom',
            'gashapon_store_sales.qtysold_ecom AS qtysold_ecom'
        )
        ->leftJoin('systems', 'gashapon_store_sales.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'gashapon_store_sales.organizations_id', '=', 'organizations.id')
        ->leftJoin('report_types', 'gashapon_store_sales.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'gashapon_store_sales.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'gashapon_store_sales.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'gashapon_store_sales.employees_id', '=', 'employees.id')
        ->leftJoin('apple_cutoffs', 'gashapon_store_sales.sales_date', '=', 'apple_cutoffs.sold_date')
        ->leftJoin('non_apple_cutoffs', 'gashapon_store_sales.sales_date', '=', 'non_apple_cutoffs.sold_date')
        ->leftJoin('all_items', 'gashapon_store_sales.item_code', '=', 'all_items.item_code');

        if (isset($ids)) {
            $query->whereIn('gashapon_store_sales.id', $ids);
        }
        return $query;
    }

    public function scopeGetYajraDefaultData($query){
        return $query->leftJoin('systems', 'gashapon_store_sales.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'gashapon_store_sales.organizations_id', '=', 'organizations.id')
        ->leftJoin('report_types', 'gashapon_store_sales.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'gashapon_store_sales.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'gashapon_store_sales.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'gashapon_store_sales.employees_id', '=', 'employees.id')
        ->select(
            'gashapon_store_sales.id',
            'gashapon_store_sales.batch_number',
            'gashapon_store_sales.is_final',
            'gashapon_store_sales.reference_number',
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            'concepts.concept_name AS concept_name',
            'gashapon_store_sales.receipt_number AS receipt_number',
            'gashapon_store_sales.sales_date AS sales_date',
            DB::raw('DATE_FORMAT(gashapon_store_sales.sales_date, "%Y") AS sales_year'),
            DB::raw('DATE_FORMAT(gashapon_store_sales.sales_date, "%m") AS sales_month'),
            'gashapon_store_sales.item_code AS item_code',
            'gashapon_store_sales.item_description AS item_description',
            DB::raw('COALESCE(gashapon_store_sales.digits_code_rr_ref, gashapon_store_sales.item_code) AS digits_code_rr_ref'),
            'gashapon_store_sales.quantity_sold AS quantity_sold',
            'gashapon_store_sales.sold_price AS sold_price',
            'gashapon_store_sales.qtysold_price AS qtysold_price',
            'gashapon_store_sales.store_cost AS store_cost',
            'gashapon_store_sales.qtysold_sc AS qtysold_sc',
            'gashapon_store_sales.net_sales AS net_sales',
            'gashapon_store_sales.sale_memo_reference AS sale_memo_reference',
            'gashapon_store_sales.current_srp AS current_srp',
            'gashapon_store_sales.qtysold_csrp AS qtysold_csrp',
            'gashapon_store_sales.dtp_rf AS dtp_rf',
            'gashapon_store_sales.qtysold_rf AS qtysold_rf',
            'gashapon_store_sales.landed_cost AS landed_cost',
            'gashapon_store_sales.qtysold_lc AS qtysold_lc',
            'gashapon_store_sales.dtp_ecom AS dtp_ecom',
            'gashapon_store_sales.qtysold_ecom AS qtysold_ecom'
        );
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