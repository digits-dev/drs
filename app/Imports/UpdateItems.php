<?php

namespace App\Imports;

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
use App\Models\System;
class UpdateItems implements ToCollection, SkipsEmptyRows, WithHeadingRow
{
    protected $table_name;
    private $system;
    public function __construct($table_name) {
        $this->table_name = $table_name;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
        
     
            // if($this->table_name == 'warehouse_inventories'){
            //     DB::table($this->table_name)
            //     ->where(['reference_number' => $row['reference_number'],
            //              'item_code' => $row['item_number'],
            //              'is_final' => 1])
            //     ->update([
            //         'inventory_transaction_types_id' => $row['inventory_type']
            //     ]);
            // }else 
            // if($this->table_name == 'store_sales'){
                DB::beginTransaction();
            	try {
                DB::table($this->table_name)
                ->where([
                        'reference_number' => $row['reference_number'],
                        'batch_number' => $row['batch_number']
                        ]
                        )
                ->update([
                    'systems_id' => $row['system'],
                    'receipt_number' => $row['receipt_number']
                ]);
                DB::commit();
                } catch (\Exception $e) {
                    \Log::debug($row['reference_number'].$e);
                    DB::rollback();
                }
            // }
            // else{
            //     DB::table($this->table_name)
            //     ->where(['reference_number' => $row['reference_number'],
            //              'item_code' => $row['item_number'],
            //              'is_final' => 1])
            //     ->update([
            //         'customers_id' => $row['customers_id']
            //     ]);
            // }
        }
    }
}