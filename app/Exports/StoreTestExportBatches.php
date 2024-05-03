<?php

namespace App\Exports;

use App\Models\ReportPrivilege;
use App\Models\StoreSale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use CRUDBooster;

class StoreTestExportBatches implements WithHeadings, WithMapping, WithCustomChunkSize, FromCollection
{
    use Exportable;
    public $filters;

    public function __construct($filter) {
        $this->userReport = ReportPrivilege::myReport(1,CRUDBooster::myPrivilegeId());
        $this->filters = $filter;
    }

    public function collection()
    {
        return StoreSale::filterForReport(StoreSale::generateReport(), $this->filters)
        ->where('is_final', 1)->limit(10000)->get();
    }
    // public function query()
    // {
    //     return StoreSale::query()->limit(100)->();
    // }

    public function headings(): array{
        return [
            'id',
            'reference_number',
            'system_name',
            'organization_name',
            'report_type',
            'channel_code',
            'customer_location',
            'store_concept_name',
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
            'item_description',
            'digits_code_rr_ref',
            'digits_code',
            'imfs_item_description',
            'upc_code',
            'upc_code2',
            'upc_code3',
            'upc_code4',
            'upc_code5',
            'brand_description',
            'category_description',
            'margin_category_description',
            'vendor_type_code',
            'inventory_type_description',
            'sku_status_description',
            'brand_status',
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
            'qtysold_rf',
            'landed_cost',
            'qtysold_lc',
            'dtp_ecom',
            'qtysold_ecom'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->reference_number,
            $transaction->system_name,
            $transaction->organization_name,
            $transaction->report_type,
            $transaction->channel_code,
            $transaction->customer_location,
            $transaction->store_concept_name,
            $transaction->receipt_number,
            $transaction->sales_date,
            $transaction->apple_yr_qtr_wk,
            $transaction->apple_week_cutoff,
            $transaction->non_apple_yr_mon_wk,
            $transaction->non_apple_week_cutoff,
            $transaction->sales_date_yr_mo,
            $transaction->sales_year,
            $transaction->sales_month,
            $transaction->item_code,
            $transaction->item_description,
            $transaction->digits_code_rr_ref,
            $transaction->digits_code,
            $transaction->imfs_item_description,
            $transaction->upc_code,
            $transaction->upc_code2,
            $transaction->upc_code3,
            $transaction->upc_code4,
            $transaction->upc_code5,
            $transaction->brand_description,
            $transaction->category_description,
            $transaction->margin_category_description,
            $transaction->vendor_type_code,
            $transaction->inventory_type_description,
            $transaction->sku_status_description,
            $transaction->brand_status,
            $transaction->quantity_sold,
            $transaction->sold_price,
            $transaction->qtysold_price,
            $transaction->store_cost,
            $transaction->qtysold_sc,
            $transaction->net_sales,
            $transaction->sale_memo_reference,
            $transaction->current_srp,
            $transaction->qtysold_csrp,
            $transaction->dtp_rf,
            $transaction->qtysold_rf,
            $transaction->landed_cost,
            $transaction->qtysold_lc,
            $transaction->dtp_ecom,
            $transaction->qtysold_ecom
        ];
    }

    public function batchSize(): int
    {
        return 10000;
    }
    public function chunkSize(): int
    {
        return 10000;
    }

    public function chunkReading(): bool
    {
        return true;
    }
}