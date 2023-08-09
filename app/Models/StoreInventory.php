<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

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

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $model->created_by = CRUDBooster::myId();
        });
        static::updating(function($model)
        {
            $model->updated_by = CRUDBooster::myId();
        });
    }
}
