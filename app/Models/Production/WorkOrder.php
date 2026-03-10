<?php

namespace App\Models\Production;

use App\Models\Inventory\InventoryMovement;
use App\Models\Inventory\Product;
use App\Models\Sales\SalesOrder;
use App\Models\System\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'sales_order_id',
        'product_id',
        'product_name',
        'quantity',
        'completion_quantity',
        'status',
        'due_date',
        'starting_date',
        'assigned_to',
        'priority',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'date',
        'starting_date' => 'date',
    ];

    // Relationships
    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventoryMovements()
    {
        return $this->morphMany(InventoryMovement::class, 'reference');
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => '#64B5F6',
            'in_progress' => '#FFB74D',
            'quality_check' => '#BA68C8',
            'completed' => '#81C784',
            'overdue' => '#EF5350',
            default => '#9E9E9E'
        };
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-orange-100 text-orange-800',
            'quality_check' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'overdue' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'low' => '#81C784',
            'medium' => '#FFB74D',
            'high' => '#EF5350',
            default => '#9E9E9E'
        };
    }

    public function getPriorityBadgeClassAttribute()
    {
        return match($this->priority) {
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->due_date < now() && $this->status !== 'completed';
    }

    public function getCompletionPercentageAttribute()
    {
        if ($this->quantity == 0) return 0;
        return round(($this->completion_quantity / $this->quantity) * 100, 2);
    }

    public function canStart()
    {
        return $this->status === 'pending';
    }

    public function canComplete()
    {
        return in_array($this->status, ['in_progress', 'quality_check']);
    }
}