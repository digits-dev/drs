<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class RunRateExport implements FromQuery, WithHeadings, WithMapping
{
    public $query;

    public function __construct($query, $cutoff_columns) {
        $this->query = $query;
        $this->cutoff_columns = $cutoff_columns;
    }

    public function headings(): array {
        return ['DIGITS CODE', ...$this->cutoff_columns];

    }

    public function map($row): array {
        return array_values($row->toArray());
    }

    public function query() {
        return $this->query->orderBy('digits_code_rr_ref', 'ASC');
    }
}
