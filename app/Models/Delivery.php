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
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
