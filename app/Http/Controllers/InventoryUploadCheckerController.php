<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\InventoryTransactionType;
use App\Models\Organization;
use App\Models\System;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class InventoryUploadCheckerController extends Controller
{
    public function check($excelFile = array(), $date) : array {
        $errors = array();
        $systems = System::active();
        $organizations = Organization::active();
        $inventoryTypes = InventoryTransactionType::active();
        $channels = Channel::active();

        //data checking ----
        $excelInventoryDate = array_unique(array_column($excelFile[0], "inventory_as_of_date"));
        foreach ($excelInventoryDate as $keyDate => $valueDate) {

            if(Carbon::parse($this->transformDate($valueDate))->format('Y-m-d') != $date){
                array_push($errors, 'inventory as of date mismatched!');
            }
        }

        $excelSystems = array_unique(array_column($excelFile[0], "system"));
        foreach ($excelSystems as $keySystem => $valueSystem) {
            $existingSystem = $systems->where('system_name',$valueSystem)->first();
            if(is_null($existingSystem)){
                array_push($errors, 'system "'.$valueSystem.'" not found!');
            }
        }

        $excelOrg = array_unique(array_column($excelFile[0], "org"));
        foreach ($excelOrg as $keyOrg => $valueOrg) {
            $existingOrg = $organizations->where('organization_name',$valueOrg)->first();
            if(is_null($existingOrg)){
                array_push($errors, 'organization "'.$valueOrg.'" not found!');
            }
        }

        $excelChannel = array_unique(array_column($excelFile[0], "channel_code"));
        foreach ($excelChannel as $keyChannel => $valueChannel) {
            $existingChannel = $channels->where('channel_code',$valueChannel)->first();
            if(is_null($existingChannel)){
                array_push($errors, 'channel code "'.$valueChannel.'" not found!');
            }
        }

        $excelInvType = array_unique(array_column($excelFile[0], "inventory_type"));
        foreach ($excelInvType as $keyInvType => $valueInvType) {
            $existingInvType = $inventoryTypes->where('inventory_transaction_type',$valueInvType)->first();
            if(is_null($existingInvType)){
                array_push($errors, 'inventory type "'.$valueInvType.'" not found!');
            }
        }

        $excelInvQty = array_unique(array_column($excelFile[0], "inventory_qty"));
        foreach ($excelInvQty as $keyInvQty => $valueInvQty) {
            if(!is_int($valueInvQty)){
                array_push($errors, '"'.$valueInvQty.'" qty is not a number!');
                break;
            }
            if(is_null($valueInvQty)){
                array_push($errors, 'blank qty "'.$valueInvQty.'" is not allowed!');
                break;
            }
            if(strpos($valueInvQty, '.') !== false){
                array_push($errors, 'decimal qty "'.$valueInvQty.'" is not allowed!');
                break;
            }
            if($valueInvQty < 0){
                array_push($errors, 'negative qty "'.$valueInvQty.'" is not allowed!');
                break;
            }
        }
        //end of data checking ----

        return $errors;
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject(intval($value)));
        } catch (\ErrorException $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }
}
