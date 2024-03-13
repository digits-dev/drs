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
    public $totals;
    public $cutoff_columns;

    public function __construct($query, $totals, $cutoff_columns) {
        $this->query = $query;
        $total_arr = [];
        foreach ($totals as $total) {
            $total = $total->toArray();
            $total = array_values($total);
            $total_arr[$total[0]] = $total[1];
        }
        $this->totals = $total_arr;
        $this->cutoff_columns = $cutoff_columns;
    }

    public function headings(): array {
        $headings = [['TOTAL', ''], ['DIGITS CODE', 'INITIAL WRR DATE', ...$this->cutoff_columns]];

        foreach ($this->cutoff_columns as $col) {
            $total = $this->totals[$col] ?? '0';
            $headings[0][] = $total;
        }
        return $headings;
    }

    public function map($row): array {
        return array_values($row->toArray());
    }

    public function query() {
        return $this->query->orderBy('digits_code_rr_ref', 'ASC');
    }
}
