<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GachaItem extends Model
{
    use HasFactory;
    protected $table = 'gacha_items';
    protected $guarded = [];
}
