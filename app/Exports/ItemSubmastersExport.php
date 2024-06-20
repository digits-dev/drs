<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;
use DB;

class ItemSubmastersExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $table_name;

    public function __construct($table_name) {
        $this->table_name = $table_name;
  
    }
    public function headings(): array {
        return [
            'ITEM CODE',
            'ITEM DESCRIPTION',
            'UPC CODE',
            'CURRENT SRP',
            'BRAND CATEGORY',
            'CATEGORY DESCRIPTION',
            'MARGIN CATEGORY DESCRIPTION',
            'VENDOR TYPE CODE',
            'INVENTORY TYPE',
            'STATUS',
            'CREATED BY',
            'CREATED AT',
            'UPDATED BY',
            'UPDATED AT'

        ];
    }

    public function map($items): array {
        if(in_array($this->table_name,['gacha_items','rma_items','items'])){
            $tableColumnName = 'digits_code';
        }else{
            $tableColumnName = 'item_code';
        }
        if($this->table_name === 'items'){
            $status = 'sku_status_description';
        }else{
            $status = 'status';
        }
        return [
            $items->$tableColumnName,
            $items->item_description,
            $items->upc_code,
            $items->current_srp,
            $items->brand_description,
            $items->category_description,
            $items->margin_category_description,
            $items->vendor_type_code,
            $items->inventory_type_description,
            $items->$status,
            $items->creator_name,
            $items->created_at,
            $items->updater_name,
            $items->updated_at    
        ];
      
    }

    public function query() {
    
        if(in_array($this->table_name,['gacha_items','rma_items','items'])){
            $tableColumnName = 'digits_code';
        }else{
            $tableColumnName = 'item_code';
        }
        if($this->table_name === 'items'){
            $status = 'sku_status_description';
        }else{
            $status = 'status';
        }
        $items = DB::table($this->table_name)->leftJoin('cms_users AS creator', $this->table_name.'.created_by', 'creator.id')
        ->leftJoin('cms_users AS updater', $this->table_name.'.updated_by', 'updater.id')
        ->select(
            $this->table_name.'.'.$tableColumnName,
            $this->table_name.'.item_description',
            $this->table_name.'.upc_code',
            $this->table_name.'.current_srp',
            $this->table_name.'.brand_description',
            $this->table_name.'.category_description',
            $this->table_name.'.margin_category_description',
            $this->table_name.'.vendor_type_code',
            $this->table_name.'.inventory_type_description',
            $this->table_name.'.'.$status,
            'creator.name AS creator_name',
            $this->table_name.'.created_at',
            'updater.name AS updater_name',
            $this->table_name.'.updated_at'
        )->orderBy($this->table_name.'.id');
   
        return $items;
    }
}
