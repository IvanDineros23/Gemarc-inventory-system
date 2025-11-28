<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'date',
        'qty',
        'remarks',
        'dr_number',
        'customer',
        'dr_date',
        'part_number',
        'item_name',
        'item_description',
        'unit_cost',
        'unit',
        'currency',
        'intended_to',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
