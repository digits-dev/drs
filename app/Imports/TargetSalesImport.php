<?php

namespace App\Imports;

use App\Models\TargetSales;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Customer;

class TargetSalesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $customer = Customer::where('customer_name', $row['customer_name'])->first();
        
        return new TargetSales([
            'stores_id' => $customer->id,
            'year' => $row['year'],
            'month' => $row['month'],
            'target_sales' => $row['target_value']
        ]);
    }
}