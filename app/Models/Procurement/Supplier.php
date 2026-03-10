<?php

namespace App\Models\Procurement;

use App\Models\Inventory\Material;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Database\Factories\SupplierFactory::new();
    }
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'payment_terms',
        'status',
        'total_orders',
        'total_spent'
    ];

    protected $casts = [
        'total_orders' => 'decimal:2',
        'total_spent' => 'decimal:2'
    ];

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}