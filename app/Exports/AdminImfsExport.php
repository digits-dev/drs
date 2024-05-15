<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\AdminItem;
use CRUDBooster;
use DB;

class AdminImfsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function headings(): array {
        return [
            'ITEM CODE',
            'ITEM DESCRIPTION',
            'CURRENT SRP',
            'STATUS',
            'CREATED BY',
            'CREATED AT',
            'UPDATED BY',
            'UPDATED AT'

        ];
    }

    public function map($admin_items): array {
        return [
            $admin_items->item_code,
            $admin_items->item_description,
            $admin_items->current_srp,
            $admin_items->status,
            $admin_items->creator_name,
            $admin_items->updater_name,

        ];
    }

    public function query() {
        $admin_items = AdminItem::leftJoin('cms_users AS creator', 'admin_items.created_by', 'creator.id')
            ->leftJoin('cms_users AS updater', 'admin_items.updated_by', 'updater.id')
            ->select(
                'admin_items.item_code',
                'admin_items.item_description',
                'admin_items.current_srp',
                'admin_items.status',
                'creator.name AS creator_name',
                'updater.name AS updater_name'
            );

        return $admin_items;
    }
}
