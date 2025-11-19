<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiving extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'fo_number',
        'date_received',
        'qty_received',
        'unit_price',
        'beginning_inventory',
        'ending_inventory',
        'details_file_path',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
