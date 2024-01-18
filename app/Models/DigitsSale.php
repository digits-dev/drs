<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class DigitsSale extends Model
{
    use HasFactory;

    protected $table = 'digits_sales';

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
        $digitsSales = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $digitsSales->reference_number + 1;
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
