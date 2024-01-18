<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitsSalesUpload extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getBatchDetails($id = null) {
        return $this->where('digits_sales_uploads.id', $id ?? $this->id)
            ->select(
                'digits_sales_uploads.*',
                'cms_users.name',
                'job_batches.created_at as started_at',
                'job_batches.finished_at',
            )
            ->leftJoin('job_batches', 'job_batches.id', 'digits_sales_uploads.job_batches_id')
            ->leftJoin('cms_users', 'cms_users.id', 'digits_sales_uploads.created_by')
            ->first();
    }

}
