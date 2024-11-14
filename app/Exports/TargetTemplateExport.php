<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TargetTemplateExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        // Return an empty collection as it's just a template
        return collect([]);
    }

    public function headings(): array
    {
        // Define the header row
        return [
            'Customer Name', 'Year', 'Month', 'Target Value'
        ];
    }
}