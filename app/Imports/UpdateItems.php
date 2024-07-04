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
class UpdateItems implements ToCollection, SkipsEmptyRows, WithHeadingRow
{
    protected $table_name;
    public function __construct($table_name) {
        $this->table_name = $table_name;
  
    }
    public function collection(Collection $rows)
    {
        foreach ($rows->toArray() as $row){
            DB::table($this->table_name)
            ->where(['reference_number' => $row['reference_number'],
                    'item_code' => $row['item_code']])
            ->update([
                'customers_id' => $row['customers_id']
            ]);
        }
    }
}