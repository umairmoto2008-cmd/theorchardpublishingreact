<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'expiry_date',
        'category_id',
        'unit',
        'purchase_price',
        'sale_price',
        'stock_quantity',
        'alert_quantity',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'alert_quantity' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->alert_quantity;
    }

    public function getMarginAttribute(): float
    {
        if ($this->sale_price > 0) {
            return round((($this->sale_price - $this->purchase_price) / $this->sale_price) * 100, 1);
        }
        return 0;
    }
}
