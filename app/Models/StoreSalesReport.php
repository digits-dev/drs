<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSalesReport extends Model
{
    use HasFactory;

    protected $table = 'store_sales_report';

    protected $fillable = [
        "reference_number",
        "channel_name",
        "customer_location",
        "store_concept_name",
        "receipt_number",
        "sales_date",
        "sales_date_yr_mo",
        "sales_year",
        "sales_month",
        "item_code",
        "item_description",
        "quantity_sold",
        "sold_price",
        "net_sales",
        "store_cost",
        "dtp_ecom",
        "sale_memo_reference",
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
        "qtysold_sc",
        "qtysold_ecom",
        "landed_cost",
        "qtysold_lc",
        "apple_yr_qtr_wk",
        "apple_week_cutoff",
        "non_apple_yr_mon_wk",
        "non_apple_week_cutoff",
        "digits_code_rr_ref"
    ];
}
