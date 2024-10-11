<?php

namespace App\Exports;
use Maatwebsite\Excel\Concerns\WithHeadings;


use Maatwebsite\Excel\Concerns\FromCollection;

class StoreInventoryExcel implements FromCollection, WithHeadings
{
    protected $data;

    // Constructor to pass sales data into the export class
    public function __construct($data)
    {
        $this->data = $data;
    }

    // This function returns the collection of data to be exported
    public function collection()
    {
        // Transform the sales data array into a collection for export
        return collect($this->data);
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
            'SUB INVENTORY',
            'CUSTOMER LOCATION',
            'INVENTORY AS OF DATE',
            'ITEM NUMBER',
            'ITEM DESCRIPTION',
            'INVENTORY QTY',
            'STORE COST',
            'STORE COST ECOMM',
            'LANDED COST',
            'PRODUCT QUALITY'
        ];
    }
}
