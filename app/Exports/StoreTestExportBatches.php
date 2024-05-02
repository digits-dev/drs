<?php

namespace App\Exports;

use App\Models\ReportPrivilege;
use App\Models\StoreSale;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class StoreTestExportBatches implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return StoreSale::query();
    }

    public function headings(): array
    {
        return [
            '#',
            'Reference_number '
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->id,
            $transaction->reference_number
        ];
    }

    public function fields(): array
    {
        return [
            'id',
            'reference_number '
        ];
    }
}