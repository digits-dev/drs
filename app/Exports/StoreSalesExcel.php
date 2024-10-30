<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class StoreSalesExcel implements FromCollection, WithHeadings
{
    protected $salesData;

    // Constructor to pass sales data into the export class
    public function __construct($salesData)
    {
        $this->salesData = $salesData;
    }

    // This function returns the collection of data to be exported
    public function collection()
    {
        // Transform the sales data array into a collection for export
        return collect($this->salesData);
    }

    // Define the headings for the Excel file
    public function headings(): array
    {
        return [
            'REFERENCE NUMBER',
            'SYSTEM',
            'ORG',
            'REPORT TYPE',
            'CHANNEL CODE',
            'CUSTOMER LOCATION',
            'RECEIPT NUMBER',
            'SOLD DATE',
            'ITEM NUMBER',
            'RR REF',
            'ITEM DESCRIPTION',
            'QTY SOLD',
            'SOLD PRICE',
            'NET SALES',
            'STORE COST',
            'STORE COST ECOMM',
            'LANDED COST',
            'SALE MEMO REF',
            'ITEM SERIAL',
            'SALES PERSON',
            'POS TRANSACTION TYPE'
        ];
    }
}

