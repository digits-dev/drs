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
        $this->totals = $totals;
        $this->cutoff_columns = $cutoff_columns;
    }

    public function headings(): array {
        $headings = [['DIGITS CODE', ...$this->cutoff_columns], []];

        foreach ($headings[0] as $key => $value) {
            $totals = $this->totals->toArray();
            foreach ($totals as $total) {
                $total_values = array_values($total);
                if (in_array($value, $total_values)) {
                    $headings[1][$key] = $total['total'];
                    continue;
                } else if (!$headings[1][$key]) {
                    $headings[1][$key] = 0;
                }
            }
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
