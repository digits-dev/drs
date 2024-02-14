<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

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
        'created_by',
        'updated_by',
    ];

    public function scopeGetNextReference($query) {
        $storeSales = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $storeSales->reference_number + 1;
    }

    public function filterForReport($filters = []) {
        $search = $filters['search'];
        $query = self::whereNotNull('store_sales.id')
            ->leftJoin('systems', 'store_sales.systems_id', '=', 'systems.id')
            ->leftJoin('organizations', 'store_sales.organizations_id', '=', 'organizations.id')
            ->leftJoin('report_types', 'store_sales.report_types_id', '=', 'report_types.id')
            ->leftJoin('channels', 'store_sales.channels_id', '=', 'channels.id')
            ->leftJoin('customers', 'store_sales.customers_id', '=', 'customers.id')
            ->leftJoin('concepts', 'customers.concepts_id', '=', 'concepts.id')
            ->leftJoin('employees', 'store_sales.employees_id', '=', 'employees.id')
            ->leftJoin('all_items', 'store_sales.item_code', '=', 'all_items.item_code');;

        if ($filters['datefrom'] && $filters['dateto']) {
            $query->whereBetween('store_sales.sales_date', [$filters['datefrom'], $filters['dateto']]);
        }
        if ($filters['channels_id']) {
            $query->where('store_sales.channels_id', $filters['channels_id']);
        }
        if ($filters['concepts_id']) {
            $query->where('customers.concepts_id', $filters['concepts_id']);
        }
        if ($search)  {
            $query->whereRaw("
                (
                    store_sales.reference_number like '%$search%' OR
                    systems.system_name like '%$search%' OR
                    report_types.report_type like '%$search%' OR
                    channels.channel_name like '%$search%' OR
                    customers.customer_name like '%$search%' OR
                    concepts.concept_name like '%$search%' OR
                    store_sales.receipt_number like '%$search%' OR
                    store_sales.sales_date = '$search'
                )
            ");
        }
        return $query->addSelect('store_sales.id');
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
