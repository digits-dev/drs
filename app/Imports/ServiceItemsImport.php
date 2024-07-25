<?php

namespace App\Imports;

use App\Models\ServiceItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use DB;
use CRUDBooster;
class ServiceItemsImport implements ToCollection, SkipsEmptyRows, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Users|null
     */
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
            DB::beginTransaction();
			try {
                ServiceItem::updateOrCreate([
                    'item_code' => $row['item_code'] 
                ],
                [
                    'item_code' => $row['item_code'],
                    'item_description' => $row['item_description'],
                    'current_srp' => $row['current_srp'],
                    'upc_code' => $row['upc_code'],
                    'brand_description' => $row['brand_description'],
                    'category_description' => $row['category_description'],
                    'margin_category_description' => $row['margin_category_description'],
                    'vendor_type_code' => $row['vendor_type_code'],
                    'inventory_type_description' => $row['inventory_type_description'],
                    'sku_status_description' => $row['sku_status_description'],
                    'brand_status' => $row['brand_status'],
                    'initial_wrr_date' => $row['initial_wrr_date']
                ]);
            DB::commit();
            } catch (\Exception $e) {
                \Log::debug($e);
                DB::rollback();
            }
        }
    }
}