<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSalesUploadLine extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function getWithHeader($id) {
        return self::leftJoin('store_sales_uploads', 'store_sales_uploads.id', 'store_sales_upload_lines.store_sales_uploads_id')
            ->where('store_sales_upload_lines.id', $id)
            ->first();
    }
}
