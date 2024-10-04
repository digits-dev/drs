<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WeeklySalesExport implements FromView
{

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }


    public function view(): View
    {
        return view('dashboard-report.store-sales.exports.weekly-sales-excel', $this->data);
    }
}
