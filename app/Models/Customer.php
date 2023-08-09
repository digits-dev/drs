<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use CRUDBooster;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'channels_id',
        'concepts_id',
        'customer_bill_to',
        'customer_name',
        'status'
    ];

    public function scopeActive($query)
    {
        return $query->where('status','ACTIVE')->get();
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
