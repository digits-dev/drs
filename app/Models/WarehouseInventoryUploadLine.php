<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryUploadLine extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function getWithHeader($id) {
        return self::leftJoin('warehouse_inventory_uploads', 'warehouse_inventory_uploads.id', 'warehouse_inventory_upload_lines.warehouse_inventory_uploads_id')
            ->where('warehouse_inventory_upload_lines.id', $id)
            ->first();
    }
}
