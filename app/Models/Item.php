<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'digits_code',
        'upc_code',
        'upc_code2',
        'upc_code3',
        'upc_code4',
        'upc_code5',
        'item_description',
        'current_srp',
        'brand_description',
        'category_description',
        'margin_category_description',
        'vendor_type_code',
        'inventory_type_description',
        'sku_status_description',
        'brand_status',
    ];
}
