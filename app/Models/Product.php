<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number',
        'inventory_id',
        'name',
        'description',
        'supplier',
        'fo_number',
        'date_received',
        'qty_received',
        'unit_price',
        'beginning_inventory',
        'ending_inventory',
        'total',
        'image_path',
    ];
}
