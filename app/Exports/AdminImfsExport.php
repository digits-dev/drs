<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use CRUDBooster;
use DB;

class AdminImfsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;
    private $table_name;

    public function __construct($table_name) {
        $this->table_name = $table_name;
  
    }
    public function headings(): array {
        if(in_array($this->table_name,['admin_items','service_items'])){
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
        }else if($this->table_name === 'employees'){
            return [
                'CHANNEL',
                'FIRST NAME',
                'LAST NAME',
                'EMPLOYEE NAME',
                'BILL TO',
                'STATUS',
                'CREATED BY',
                'CREATED AT',
                'UPDATED BY',
                'UPDATED AT'
            ];
        }else{
            return [
                'CHANNEL',
                'POS TYPE',
                'TRADE NAME',
                'MALL',
                'BRANCH',
                'CUSTOMER BILL TO',
                'BILL TO',
                'CUSTOMER NAME',
                'CUSTOMER STATUS',
                'STATUS',
                'CONCEPT NAME',
                'CREATED BY',
                'CREATED AT',
                'UPDATED BY',
                'UPDATED AT'
            ];
        }
    }

    public function map($admin_items): array {
        if(in_array($this->table_name,['admin_items','service_items'])){
            return [
                $admin_items->item_code,
                $admin_items->item_description,
                $admin_items->current_srp,
                $admin_items->status,
                $admin_items->creator_name,
                $admin_items->created_at,
                $admin_items->updater_name,
                $admin_items->updated_at    
            ];
        }else if($this->table_name === 'employees'){
            return [
                $admin_items->channel_name,
                $admin_items->first_name,
                $admin_items->last_name,
                $admin_items->employee_name,
                $admin_items->bill_to,
                $admin_items->status,
                $admin_items->creator_name,
                $admin_items->created_at,
                $admin_items->updater_name,
                $admin_items->updated_at    
    
            ];
        }else{
            return [
                $admin_items->channel_name,
                $admin_items->pos_type_name,
                $admin_items->trade_name,
                $admin_items->mall,
                $admin_items->branch,
                $admin_items->customer_bill_to,
                $admin_items->bill_to,
                $admin_items->customer_name,
                $admin_items->customer_status,
                $admin_items->status,
                $admin_items->concept_name,
                $admin_items->creator_name,
                $admin_items->created_at,
                $admin_items->updater_name,
                $admin_items->updated_at    
    
            ];
        }
       
    }

    public function query() {
        if(in_array($this->table_name,['admin_items','service_items'])){
            $admin_items = DB::table($this->table_name)->leftJoin('cms_users AS creator', $this->table_name.'.created_by', 'creator.id')
            ->leftJoin('cms_users AS updater', $this->table_name.'.updated_by', 'updater.id')
            ->select(
                $this->table_name.'.item_code',
                $this->table_name.'.item_description',
                $this->table_name.'.current_srp',
                $this->table_name.'.status',
                'creator.name AS creator_name',
                $this->table_name.'.created_at',
                'updater.name AS updater_name',
                $this->table_name.'.updated_at'
            )->orderBy($this->table_name.'.id');
        }else if(in_array($this->table_name,['customers'])){
            $admin_items = DB::table($this->table_name)->leftJoin('cms_users AS creator', $this->table_name.'.created_by', 'creator.id')
            ->leftJoin('cms_users AS updater', $this->table_name.'.updated_by', 'updater.id')
            ->leftJoin('channels', $this->table_name.'.channels_id', 'channels.id')
            ->leftJoin('concepts', $this->table_name.'.concepts_id', 'concepts.id')
            ->leftJoin('pos_types', $this->table_name.'.pos_types_id', 'pos_types.id')
            ->select(
                'channels.channel_name',
                'pos_types.pos_type_name',
                $this->table_name.'.trade_name',
                $this->table_name.'.mall',
                $this->table_name.'.branch',
                $this->table_name.'.customer_bill_to',
                $this->table_name.'.bill_to',
                $this->table_name.'.customer_name',
                $this->table_name.'.customer_status',
                $this->table_name.'.status',
                'concepts.concept_name',
                'creator.name AS creator_name',
                $this->table_name.'.created_at',
                'updater.name AS updater_name',
                $this->table_name.'.updated_at'
            )->orderBy($this->table_name.'.id');
        }else if(in_array($this->table_name,['employees'])){
            $admin_items = DB::table($this->table_name)->leftJoin('cms_users AS creator', $this->table_name.'.created_by', 'creator.id')
            ->leftJoin('cms_users AS updater', $this->table_name.'.updated_by', 'updater.id')
            ->leftJoin('channels', $this->table_name.'.channels_id', 'channels.id')
            ->select(
                'channels.channel_name',
                $this->table_name.'.first_name',
                $this->table_name.'.last_name',
                $this->table_name.'.employee_name',
                $this->table_name.'.bill_to',
                $this->table_name.'.status',
                'creator.name AS creator_name',
                $this->table_name.'.created_at',
                'updater.name AS updater_name',
                $this->table_name.'.updated_at'
            )->orderBy($this->table_name.'.id');
        }
       
        return $admin_items;
    }
}
