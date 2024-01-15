<?php

namespace App\Exports;

use App\Models\ReportPrivilege;
use App\Models\StoreSalesReport;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;
use CRUDBooster;

class StoreSalesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $userReport;

    public function __construct() {
        $this->userReport = ReportPrivilege::myReport(1,3);
    }

    public function headings(): array {
        return explode(",",$this->userReport->report_header);

    }

    public function map($item): array {

        $sales = explode("`,`",$this->userReport->report_query);
        $salesReport = [];

        foreach ($sales as $key => $value) {
            array_push($salesReport,$item->$value);
        }

        return $salesReport;
    }

    public function query()
    {
        // dd(request()->receipt_number);
        if ( request()->receipt_number == null) {
            $salesReport = StoreSalesReport::selectRaw("`".$this->userReport->report_query."`");
        }else {
            $salesReport = StoreSalesReport::selectRaw("`".$this->userReport->report_query."`")->where('channel_name', request()->channel)
            ->where('receipt_number', request()->receipt_number)->whereBetween('sales_date', [request()->datefrom, request()->dateto]);
        }
    
        
        // if (request()->has('filter_column')) {
        //     $filter_column = request()->filter_column;

        //     $salesReport->where(function($w) use ($filter_column) {
        //         foreach($filter_column as $key=>$fc) {

        //             $value = @$fc['value'];
        //             $type  = @$fc['type'];

        //             if($type == 'empty') {
        //                 $w->whereNull($key)->orWhere($key,'');
        //                 continue;
        //             }

        //             if($value=='' || $type=='') continue;

        //             if($type == 'between') continue;

        //             switch($type) {
        //                 default:
        //                     if($key && $type && $value) $w->where($key,$type,$value);
        //                 break;
        //                 case 'like':
        //                 case 'not like':
        //                     $value = '%'.$value.'%';
        //                     if($key && $type && $value) $w->where($key,$type,$value);
        //                 break;
        //                 case 'in':
        //                 case 'not in':
        //                     if($value) {
        //                         $value = explode(',',$value);
        //                         if($key && $value) $w->whereIn($key,$value);
        //                     }
        //                 break;
        //             }
        //         }
        //     });

        //     foreach($filter_column as $key=>$fc) {
        //         $value = @$fc['value'];
        //         $type  = @$fc['type'];
        //         $sorting = @$fc['sorting'];

        //         if($sorting!='') {
        //             if($key) {
        //                 $salesReport->orderby($key,$sorting);
        //                 $filter_is_orderby = true;
        //             }
        //         }

        //         if ($type=='between') {
        //             if($key && $value) $salesReport->whereBetween($key,$value);
        //         }

        //         else {
        //             continue;
        //         }
        //     }
        // }


        return $salesReport;
    }
}