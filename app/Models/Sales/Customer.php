<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'customer_type', 'phone', 'email'];

    public function salesOrders()
    {
        return $this->hasMany(SalesOrder::class);
    }

    public function totalOrders()
    {
        return $this->salesOrders()->where('status', '!=', 'Cancelled')->count();
    }

    public function totalSpent()
    {
        return $this->salesOrders()->where('status', '!=', 'Cancelled')->sum('total_amount');
    }

}
