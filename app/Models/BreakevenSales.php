<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakevenSales extends Model
{
    use HasFactory;
    protected $table = 'breakeven_sales';

    protected $fillable = [
        'stores_id',
        'year',
        'month',
        'breakeven'
    ];
}