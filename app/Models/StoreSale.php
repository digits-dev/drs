<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;
use Illuminate\Support\Facades\DB;

class StoreSale extends Model
{
    use HasFactory;

    protected $table = 'store_sales';

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
        'item_serial',
        'sales_person',
        'pos_transaction_type',
        'created_by',
        'updated_by',
    ];

    public function scopeGetNextReference($query) {
        $storeSales = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $storeSales->reference_number + 1;
    }

    public function filterForReport($query, $filters = [], $is_upload = false) {
        $search = $filters['search'];
        
        if ($filters['datefrom'] && $filters['dateto']) {
            $query->whereBetween('store_sales.sales_date', [$filters['datefrom'], $filters['dateto']]);
        }
    
        if ($filters['channels_id']) {
            $query->where('store_sales.channels_id', $filters['channels_id']);
        }
        if ($filters['concepts_id']) {
            $query->where('customers.concepts_id', $filters['concepts_id']);
        }
        if ($filters['receipt_number']) {
            $query->where('store_sales.receipt_number', $filters['receipt_number']);
        }
        // if (isset($search))  {
        //     $search_filter = "
        //         store_sales.reference_number LIKE '%$search%' OR
        //         systems.system_name LIKE '%$search%' OR
        //         report_types.report_type LIKE '%$search%' OR
        //         channels.channel_name LIKE '%$search%' OR
        //         customers.customer_name LIKE '%$search%' OR
        //         concepts.concept_name LIKE '%$search%' OR
        //         store_sales.receipt_number LIKE '%$search%' OR
        //         store_sales.sales_date LIKE '%$search%'
        //     ";
        //     if ($is_upload) {
        //         $search_filter .= "
        //             OR customers.customer_name LIKE '%$search%'
        //             OR employees.employee_name LIKE '%$search%'
        //             OR store_sales.item_code LIKE '%$search%'
        //             OR all_items.item_description LIKE '%$search%'
        //             OR store_sales.item_description LIKE '%$search%'
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
        $query = StoreSale::select(
            'store_sales.id',
            'store_sales.batch_number',
            'store_sales.is_final',
            'store_sales.reference_number',
            DB::raw('all_items.digits_code IS NOT NULL AS rr_flag'),
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            'concepts.concept_name AS concept_name',
            'store_sales.receipt_number AS receipt_number',
            'store_sales.sales_date AS sales_date',
            'apple_cutoffs.apple_yr_qtr_wk AS apple_yr_qtr_wk',
            'apple_cutoffs.apple_week_cutoff AS apple_week_cutoff',
            'non_apple_cutoffs.non_apple_yr_mon_wk AS non_apple_yr_mon_wk',
            'non_apple_cutoffs.non_apple_week_cutoff AS non_apple_week_cutoff',
            DB::raw('DATE_FORMAT(store_sales.sales_date, "%Y_%m") AS sales_date_yr_mo'),
            DB::raw('DATE_FORMAT(store_sales.sales_date, "%Y") AS sales_year'),
            DB::raw('DATE_FORMAT(store_sales.sales_date, "%m") AS sales_month'),
            'store_sales.item_code AS item_code',
            'store_sales.item_description AS item_description',
            DB::raw('COALESCE(store_sales.digits_code_rr_ref, store_sales.item_code) AS digits_code_rr_ref'),
            'all_items.item_code AS digits_code',
            DB::raw('COALESCE(all_items.item_description, store_sales.item_description) AS imfs_item_description'),
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
            'store_sales.quantity_sold AS quantity_sold',
            'store_sales.sold_price AS sold_price',
            'store_sales.qtysold_price AS qtysold_price',
            'store_sales.store_cost AS store_cost',
            'store_sales.qtysold_sc AS qtysold_sc',
            'store_sales.net_sales AS net_sales',
            'store_sales.sale_memo_reference AS sale_memo_reference',
            'store_sales.current_srp AS current_srp',
            'store_sales.qtysold_csrp AS qtysold_csrp',
            'store_sales.dtp_rf AS dtp_rf',
            'store_sales.qtysold_rf AS qtysold_rf',
            'store_sales.landed_cost AS landed_cost',
            'store_sales.qtysold_lc AS qtysold_lc',
            'store_sales.dtp_ecom AS dtp_ecom',
            'store_sales.qtysold_ecom AS qtysold_ecom',
            'store_sales.item_serial AS item_serial',
            'store_sales.sales_person AS sales_person',
            'store_sales.pos_transaction_type AS pos_transaction_type'
        )
        ->leftJoin('systems', 'store_sales.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'store_sales.organizations_id', '=', 'organizations.id')
        ->leftJoin('report_types', 'store_sales.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'store_sales.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'store_sales.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'store_sales.employees_id', '=', 'employees.id')
        ->leftJoin('apple_cutoffs', 'store_sales.sales_date', '=', 'apple_cutoffs.sold_date')
        ->leftJoin('non_apple_cutoffs', 'store_sales.sales_date', '=', 'non_apple_cutoffs.sold_date')
        ->leftJoin('all_items', 'store_sales.item_code', '=', 'all_items.item_code');

        if (isset($ids)) {
            $query->whereIn('store_sales.id', $ids);
        }
        return $query;
    }

    public function scopeGetYajraDefaultData($query){
        return $query->leftJoin('systems', 'store_sales.systems_id', '=', 'systems.id')
        ->leftJoin('organizations', 'store_sales.organizations_id', '=', 'organizations.id')
        ->leftJoin('report_types', 'store_sales.report_types_id', '=', 'report_types.id')
        ->leftJoin('channels', 'store_sales.channels_id', '=', 'channels.id')
        ->leftJoin('customers', 'store_sales.customers_id', '=', 'customers.id')
        ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
        ->leftJoin('employees', 'store_sales.employees_id', '=', 'employees.id')
        ->select(
            'store_sales.id',
            'store_sales.batch_number',
            'store_sales.is_final',
            'store_sales.reference_number',
            'systems.system_name AS system_name',
            'organizations.organization_name AS organization_name',
            'report_types.report_type AS report_type',
            'channels.channel_code AS channel_code',
            DB::raw('COALESCE(customers.customer_name, employees.employee_name) AS customer_location'),
            'concepts.concept_name AS concept_name',
            'store_sales.receipt_number AS receipt_number',
            'store_sales.sales_date AS sales_date',
            DB::raw('DATE_FORMAT(store_sales.sales_date, "%Y") AS sales_year'),
            DB::raw('DATE_FORMAT(store_sales.sales_date, "%m") AS sales_month'),
            'store_sales.item_code AS item_code',
            'store_sales.item_description AS item_description',
            DB::raw('COALESCE(store_sales.digits_code_rr_ref, store_sales.item_code) AS digits_code_rr_ref'),
            'store_sales.quantity_sold AS quantity_sold',
            'store_sales.sold_price AS sold_price',
            'store_sales.qtysold_price AS qtysold_price',
            'store_sales.store_cost AS store_cost',
            'store_sales.qtysold_sc AS qtysold_sc',
            'store_sales.net_sales AS net_sales',
            'store_sales.sale_memo_reference AS sale_memo_reference',
            'store_sales.current_srp AS current_srp',
            'store_sales.qtysold_csrp AS qtysold_csrp',
            'store_sales.dtp_rf AS dtp_rf',
            'store_sales.qtysold_rf AS qtysold_rf',
            'store_sales.landed_cost AS landed_cost',
            'store_sales.qtysold_lc AS qtysold_lc',
            'store_sales.dtp_ecom AS dtp_ecom',
            'store_sales.qtysold_ecom AS qtysold_ecom'
        )->where('is_final', 1)->limit(10)->get();
    }

    //FROM ETP
    public function scopeGetStoresSalesFromPosEtp($query,$datefrom,$dateto){
        $query = 
        DB::connection('sqlsrv')->select(DB::raw("SET NOCOUNT ON; exec [SP_Custom_SalesDiscReport] 100,'100',$datefrom,$dateto"));
        // DB::connection('sqlsrv')->select(DB::raw("
        //     SELECT 
        //         C.Warehouse AS 'STORE ID',
        //         C.InvoiceNumber AS 'RECEIPT #',
        //         C.CreateDate AS 'SOLD DATE',
        //         C.ItemNumber AS 'ITEM NUMBER',
        //         C.InvoiceQuantity AS 'QTY SOLD',
        //         C.SalesPrice AS 'SOLD PRICE',
        //         C.LotNumber AS 'ITEM SERIAL',
        //         C.SalesPerson AS 'SALES PERSON'
              
        //     FROM CashOrderTrn C (nolock)
        //     WHERE 
        //         C.Company = 100
        //         AND C.Division = '100'
        //         AND C.InvoiceType = 31
        //         AND C.FreeField2 = '0'
        //         AND C.CreateDate BETWEEN :datefrom AND :dateto
        // "), [
        //     'datefrom' => $datefrom,
        //     'dateto' => $dateto,
        // ]);
    
        return $query;
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            if (CRUDBooster::myId()) {
                $model->created_by = CRUDBooster::myId();
            }else{
                $model->created_by = 136;
            }
        });
        static::updating(function($model)
        {
            if (CRUDBooster::myId()) {
                $model->updated_by = CRUDBooster::myId();
            }else{
                $model->updated_by = 136;
            }
        });
    }
}
