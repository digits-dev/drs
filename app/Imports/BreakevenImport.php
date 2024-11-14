<?php

namespace App\Imports;

use App\Models\BreakevenSales;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Customer;

class BreakevenImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $customer = Customer::where('customer_name', $row['customer_name'])->first();
        
        return new BreakevenSales([
            'stores_id' => $customer->id,
            'year' => $row['year'],
            'month' => $row['month'],
            'breakeven' => $row['breakeven_value']
        ]);
    }
}