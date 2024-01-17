<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitsSalesUploadLine extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getWithHeader($id) {
        return self::leftJoin('digits_sales_uploads', 'digits_sales_uploads.id', 'digits_sales_upload_lines.digits_sales_uploads_id')
            ->where('digits_sales_upload_lines.id', $id)
            ->first();
    }
}
