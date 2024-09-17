<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierIntransitInventoryUploadLine extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function getWithHeader($id) {
        return self::leftJoin('supplier_intransit_inventory_uploads', 'supplier_intransit_inventory_uploads.id', 'supplier_intransit_inventory_upload_lines.supp_intransit_inventory_uploads_id')
            ->where('supplier_intransit_inventory_upload_lines.id', $id)
            ->first();
    }
}
