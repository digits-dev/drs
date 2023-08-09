<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class StoreInventoryExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
