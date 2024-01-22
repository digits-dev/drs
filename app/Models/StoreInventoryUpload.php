<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventoryUpload extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getBatchDetails($id = null) {
        return $this->where('store_inventory_uploads.id', $id ?? $this->id)
            ->select(
                'store_inventory_uploads.*',
                'cms_users.name',
                'job_batches.created_at as started_at',
                'job_batches.finished_at',
            )
            ->leftJoin('job_batches', 'job_batches.id', 'store_inventory_uploads.job_batches_id')
            ->leftJoin('cms_users', 'cms_users.id', 'store_inventory_uploads.created_by')
            ->first();
    }

}
