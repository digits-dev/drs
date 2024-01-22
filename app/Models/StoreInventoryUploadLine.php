<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventoryUploadLine extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function getWithHeader($id) {
        return self::leftJoin('store_inventory_uploads', 'store_inventory_uploads.id', 'store_inventory_upload_lines.store_inventory_uploads_id')
            ->where('store_inventory_upload_lines.id', $id)
            ->first();
    }
}
