<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class WarehouseInventory extends Model
{
    use HasFactory;

    protected $table = 'warehouse_inventories';

    protected $fillable = [];

    public function scopeGetNextReference($query) {
        $warehouseInventory = $query->orderBy('id','DESC')
            ->orderBy('reference_number','DESC')
            ->select('reference_number')->first();

        return $warehouseInventory->reference_number + 1;
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $model->created_by = CRUDBooster::myId();
        });
        static::updating(function($model)
        {
            $model->updated_by = CRUDBooster::myId();
        });
    }
}
