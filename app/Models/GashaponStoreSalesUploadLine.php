<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GashaponStoreSalesUploadLine extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function getWithHeader($id) {
        return self::leftJoin('gashapon_store_sales_uploads', 'gashapon_store_sales_uploads.id', 'gashapon_store_sales_upload_lines.store_sales_uploads_id')
            ->where('gashapon_store_sales_upload_lines.id', $id)
            ->first();
    }
}
