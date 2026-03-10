<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'product_name',
        'description',
        'category',
        'production_cost',
        'selling_price',
        'current_stock',
        'minimum_stock',
        'unit',
        'status'
    ];

    protected $casts = [
        'production_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class, 'item_id')->where('item_type', 'product');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'product_materials')
            ->withPivot('quantity_needed')
            ->withTimestamps();
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->production_cost > 0) {
            return (($this->selling_price - $this->production_cost) / $this->production_cost) * 100;
        }
        return 0;
    }
}
