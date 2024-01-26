<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryUpload extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getBatchDetails($id = null) {
        return $this->where('warehouse_inventory_uploads.id', $id ?? $this->id)
            ->select(
                'warehouse_inventory_uploads.*',
                'cms_users.name',
                'job_batches.created_at as started_at',
                'job_batches.finished_at',
            )
            ->leftJoin('job_batches', 'job_batches.id', 'warehouse_inventory_uploads.job_batches_id')
            ->leftJoin('cms_users', 'cms_users.id', 'warehouse_inventory_uploads.created_by')
            ->first();
    }

    public function appendNewError($error_message) {
        $error_arr = json_decode($this->error) ?: [];
        $error_arr[] = $error_message;
        $this->errors = json_encode($error_arr);
        $this->save();
    }
}
