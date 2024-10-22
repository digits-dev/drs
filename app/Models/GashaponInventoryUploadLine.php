<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GashaponInventoryUploadLine extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function getWithHeader($id) {
        return self::leftJoin('gashapon_inventory_uploads', 'gashapon_inventory_uploads.id', 'gashapon_inventory_upload_lines.gashapon_inventory_uploads_id')
            ->where('gashapon_inventory_upload_lines.id', $id)
            ->first();
    }
}
