<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_return_id',
        'product_id',
        'product_name',
        'quantity',
        'unit_price',
        'refund_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
