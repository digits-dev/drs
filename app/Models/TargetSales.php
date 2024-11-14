<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetSales extends Model
{
    use HasFactory;
    protected $table = 'target_sales';

    protected $fillable = [
        'stores_id',
        'year',
        'month',
        'target_sales'
    ];
}