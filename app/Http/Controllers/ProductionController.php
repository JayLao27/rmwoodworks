<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\InventoryMovement;
use App\Models\Material;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\WorkOrder;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        // Paginate work orders instead of loading all
        $workOrders = WorkOrder::with(['salesOrder.customer', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        // Get status counts with COUNT queries, not loading all data
        $statusCounts = [
            'pending' => WorkOrder::where('status', 'pending')->count(),
            'in_progress' => WorkOrder::where('status', 'in_progress')
                ->where(function($q) {
                    $q->whereNull('due_date')
                      ->orWhere('due_date', '>=', now()->startOfDay());
                })->count(),
            'quality_check' => WorkOrder::where('status', 'quality_check')->count(),
            'completed' => WorkOrder::where('status', 'completed')->count(),
            'overdue' => WorkOrder::where(function($q) {
                $q->where('status', 'overdue')
                  ->orWhere(function($sub) {
                      $sub->where('status', '!=', 'completed')
                          ->where('status', '!=', 'cancelled')
                          ->where('due_date', '<', now()->startOfDay());
                  });
            })->count(),
            'cancelled' => WorkOrder::where('status', 'cancelled')->count(),
        ];

        // Get pending sales orders with better query filtering
        // Use DB subquery instead of map/filter in PHP
        $pendingSalesOrders = SalesOrder::with(['customer', 'items.product', 'workOrders'])
            ->whereNotIn('status', ['Delivered', 'Cancelled'])
            ->whereHas('items', function($query) {
                // Only get orders that have items without corresponding work orders (excluding cancelled)
                $query->whereNotExists(function($subQuery) {
                    $subQuery->select(DB::raw(1))
                        ->from('work_orders')
                        ->whereColumn('work_orders.product_id', 'sales_order_items.product_id')
                        ->whereColumn('work_orders.sales_order_id', 'sales_order_items.sales_order_id')
                        ->where('work_orders.status', '!=', 'cancelled');
                });
            })
            ->orderBy('delivery_date')
            ->paginate(15);

        $pendingItemsCount = (int) SalesOrderItem::whereHas('salesOrder', function($query) {
            $query->whereNotIn('status', ['Delivered', 'Cancelled']);
        })
        ->whereNotExists(function($subQuery) {
            $subQuery->select(DB::raw(1))
                ->from('work_orders')
                ->whereColumn('work_orders.product_id', 'sales_order_items.product_id')
                ->whereColumn('work_orders.sales_order_id', 'sales_order_items.sales_order_id')
                ->where('work_orders.status', '!=', 'cancelled');
        })
        ->count();

        // Use pending items count instead of recalculating from pending orders
        $statusCounts['pending'] = $pendingItemsCount;

        // Color maps for modals
        $customerTypeBg = [
            'Wholesale' => '#64B5F6',
            'Retail' => '#6366F1',
            'Contractor' => '#BA68C8',
        ];
        $statusBg = [
            'In production' => '#FFB74D',
            'Pending' => '#64B5F6',
            'Delivered' => '#81C784',
            'Ready' => '#BA68C8',
        ];
        $paymentBg = [
            'Pending' => '#ffffff',
            'Partial' => '#FFB74D',
            'Paid' => '#81C784',
        ];

        return view('Systems.production', compact('workOrders', 'statusCounts', 'pendingSalesOrders', 'pendingItemsCount', 'customerTypeBg', 'statusBg', 'paymentBg'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sales_order_id' => 'required|exists:sales_orders,id',
                'due_date' => 'required|date',
                'assigned_to' => 'required|string|max:255',
                'priority' => 'nullable|in:low,medium,high',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.selected' => 'nullable|boolean',
            ]);

            $selectedItems = array_filter($validated['items'], function($item) {
                return isset($item['selected']) && $item['selected'];
            });

            if (empty($selectedItems)) {
                return response()->json(['success' => false, 'message' => 'Please select at least one product.'], 400);
            }

            $salesOrder = SalesOrder::with('items')->findOrFail($validated['sales_order_id']);
            $createdWorkOrders = [];

            DB::beginTransaction();

            foreach ($selectedItems as $itemData) {
                $productId = $itemData['product_id'];
                $quantity = (int)$itemData['quantity'];

                $product = Product::with('materials')->findOrFail($productId);

                // Check for existing active work order (excluding cancelled)
                $alreadyExists = WorkOrder::where('sales_order_id', $validated['sales_order_id'])
                    ->where('product_id', $productId)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if ($alreadyExists) {
                    throw new \Exception('A work order for "' . $product->product_name . '" already exists for this sales order.');
                }

                // Ensure this product/quantity is from this sales order
                $lineItem = $salesOrder->items->firstWhere('product_id', $productId);
                if (!$lineItem || $lineItem->quantity < $quantity) {
                    throw new \Exception('Invalid product or quantity for "' . $product->product_name . '".');
                }

                // Check material availability (BOM)
                foreach ($product->materials as $material) {
                    $qtyNeeded = (float)$material->pivot->quantity_needed * $quantity;
                    if ($material->current_stock < $qtyNeeded) {
                        throw new \Exception(sprintf(
                            'Insufficient stock for "%s". Required for %s: %s %s, Available: %s %s.',
                            $material->name,
                            $product->product_name,
                            number_format($qtyNeeded, 2),
                            $material->unit,
                            number_format($material->current_stock, 2),
                            $material->unit
                        ));
                    }

                    // Stock out: create inventory movements (out) and deduct material stock
                    InventoryMovement::create([
                        'item_type' => 'material',
                        'item_id' => $material->id,
                        'movement_type' => 'out',
                        'quantity' => $qtyNeeded,
                        'reference_type' => WorkOrder::class,
                        'reference_id' => 0, // Temporary, will update after WO creation
                        'notes' => sprintf('Production work order – %s x %s (Stock Out: %s)', $product->product_name, $quantity, now()->toDateTimeString()),
                        'status' => 'completed',
                        'user_id' => auth()->id()
                    ]);

                    $material->decrement('current_stock', $qtyNeeded);
                }

                $orderNumber = $this->generateWorkOrderNumber();
                $workOrder = WorkOrder::create([
                    'order_number' => $orderNumber,
                    'sales_order_id' => $validated['sales_order_id'],
                    'product_id' => $productId,
                    'product_name' => $product->product_name,
                    'quantity' => $quantity,
                    'due_date' => $validated['due_date'],
                    'assigned_to' => $validated['assigned_to'],
                    'priority' => $validated['priority'] ?? 'medium',
                    'status' => 'in_progress',
                    'user_id' => auth()->id(),
                ]);

                // Update inventory movements with the correct reference_id
                InventoryMovement::where('reference_type', WorkOrder::class)
                    ->where('reference_id', 0)
                    ->update(['reference_id' => $workOrder->id]);

                $createdWorkOrders[] = $workOrder;
            }

            $count = count($createdWorkOrders);
            $productNames = collect($createdWorkOrders)->pluck('product_name')->implode(', ');
            \App\Models\SystemActivity::log('Production', 'Work Orders Created', "{$count} work orders created for: {$productNames}.", 'purple');

            // Automatically update Sales Order status to In production
            if ($salesOrder->status === 'Pending') {
                $salesOrder->update(['status' => 'In production']);
            }

            DB::commit();

            $count = count($createdWorkOrders);
            $message = $count . ' work order' . ($count > 1 ? 's' : '') . ' created. Materials have been deducted from stock.';
            
            if ($request->wantsJson()) {
                // Return HTML for all created rows if needed, or just reload
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->back()->with('error', $message);
        }
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        try {
            $validated = $request->validate([
                'status' => 'nullable|in:pending,in_progress,quality_check,completed,overdue,cancelled',
                'completion_quantity' => 'nullable|integer|min:0',
                'notes' => 'nullable|string|max:1000',
            ]);

            $updateData = [];
            if (isset($validated['status'])) {
                $updateData['status'] = $validated['status'];
            }
            if (isset($validated['completion_quantity'])) {
                $updateData['completion_quantity'] = $validated['completion_quantity'];
            }
            if (isset($validated['notes'])) {
                $updateData['notes'] = $validated['notes'];
            }
            $updateData['user_id'] = auth()->id();

            if (!empty($updateData)) {
                $workOrder->update($updateData);
            }

            $message = 'Work order updated successfully.';
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $message = 'Validation failed: ' . implode(', ', array_reduce($e->errors(), function ($carry, $item) {
                return array_merge($carry, $item);
            }, []));
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            $message = 'Error updating work order: ' . $e->getMessage();
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return redirect()->back()->with('error', $message);
        }
    }

    public function start(WorkOrder $workOrder)
    {
        if ($workOrder->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending work orders can be started.');
        }
        $workOrder->update([
            'status' => 'in_progress',
            'starting_date' => now()->toDateString()
        ]);

        // Ensure Sales Order status is In production
        if ($workOrder->salesOrder && $workOrder->salesOrder->status === 'Pending') {
            $workOrder->salesOrder->update(['status' => 'In production']);
        }
        return redirect()->back()->with('success', 'Work order started.');
    }

    public function complete(WorkOrder $workOrder, Request $request)
    {
        if ($workOrder->status === 'completed') {
            $message = 'Work order is already completed.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            return redirect()->back()->with('info', $message);
        }
        
        $workOrder->update([
            'status' => 'completed',
            'completion_quantity' => $workOrder->quantity,
        ]);

        $workOrder->loadMissing(['product', 'salesOrder']);
    
        // Update product inventory (Stock In)
        if ($workOrder->product) {
            $qtyCompleted = $workOrder->completion_quantity > 0 ? $workOrder->completion_quantity : $workOrder->quantity;
            
            // Record the inventory movement for the finished product
            InventoryMovement::create([
                'item_type' => 'product',
                'item_id' => $workOrder->product_id,
                'movement_type' => 'in',
                'quantity' => $qtyCompleted,
                'reference_type' => WorkOrder::class,
                'reference_id' => $workOrder->id,
                'notes' => sprintf('Production completed for WO-%s (Stock In: %s)', $workOrder->order_number, now()->toDateTimeString()),
                'status' => 'completed',
                'user_id' => auth()->id()
            ]);

            // Increment current stock of the product
            $workOrder->product->increment('current_stock', $qtyCompleted);
        }

        // Check if all work orders for this sales order are completed
        if ($workOrder->salesOrder) {
            $totalWorkOrders = $workOrder->salesOrder->workOrders()->where('status', '!=', 'cancelled')->count();
            $completedWorkOrders = $workOrder->salesOrder->workOrders()->where('status', 'completed')->count();
            
            if ($totalWorkOrders > 0 && $totalWorkOrders === $completedWorkOrders) {
                $workOrder->salesOrder->update(['status' => 'Ready']);
            }
        }
        
        \App\Models\SystemActivity::log('Production', 'Work Order Completed', "Work Order #{$workOrder->order_number} for {$workOrder->product_name} has been completed.", 'emerald');

        $message = 'Work order marked as completed and inventory updated.';
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        return redirect()->back()->with('success', $message);
    }

    public function show(WorkOrder $workOrder)
    {
        $workOrder->load(['product', 'salesOrder.customer']);

        $data = [
            'id' => $workOrder->id,
            'order_number' => $workOrder->order_number,
            'product_name' => $workOrder->product_name ?? $workOrder->product->product_name ?? 'Unknown',
            'quantity' => $workOrder->quantity,
            'completion_quantity' => $workOrder->completion_quantity,
            'starting_date' => $workOrder->created_at ? $workOrder->created_at->toIso8601String() : null,
            'due_date' => $workOrder->due_date ? $workOrder->due_date->toIso8601String() : null,
            'assigned_to' => $workOrder->assigned_to,
            'status' => $workOrder->status,
            'notes' => $workOrder->notes ?? '', 
        ];

        return response()->json(['success' => true, 'workOrder' => $data]);
    }

    public function cancel(WorkOrder $workOrder)
    {
        try {
            if ($workOrder->status === 'completed' || $workOrder->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel a completed or already cancelled work order.'
                ], 400);
            }

            $workOrder->update([
                'status' => 'cancelled',
                'completion_quantity' => 0,
            ]);

            // Revert Sales Order status to Pending if no other active work orders exist
            if ($workOrder->salesOrder) {
                $hasActiveWorkOrders = $workOrder->salesOrder->workOrders()
                    ->where('status', '!=', 'cancelled')
                    ->exists();
                
                if (!$hasActiveWorkOrders) {
                    $workOrder->salesOrder->update(['status' => 'Pending']);
                }
            }

            // Release reserved materials back to inventory
            if ($workOrder->product && $workOrder->product->materials) {
                foreach ($workOrder->product->materials as $material) {
                    $qtyToRelease = (float) $material->pivot->quantity_needed * (int) $workOrder->quantity;
                    $material->increment('current_stock', $qtyToRelease);

                    // Record the inventory movement
                    InventoryMovement::create([
                        'item_type' => 'material',
                        'item_id' => $material->id,
                        'movement_type' => 'in',
                        'quantity' => $qtyToRelease,
                        'reference_type' => WorkOrder::class,
                        'reference_id' => $workOrder->id,
                        'notes' => 'Materials released from cancelled work order ' . $workOrder->order_number . ' (Stock In: ' . now()->toDateTimeString() . ')',
                        'user_id' => auth()->id()
                    ]);
                }
            }
            \App\Models\SystemActivity::log('Production', 'Work Order Cancelled', "Work Order #{$workOrder->order_number} was cancelled.", 'red');

            return response()->json([
                'success' => true,
                'message' => 'Work order cancelled successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling work order: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateWorkOrderNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'WO-' . $year . '-';

        $last = WorkOrder::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        $nextSeq = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seqPart = end($parts);
            $num = (int) ltrim($seqPart, '0');
            $nextSeq = $num + 1;
        }

        do {
            $candidate = sprintf('%s%03d', $prefix, $nextSeq);
            $exists = WorkOrder::where('order_number', $candidate)->exists();
            if (!$exists) {
                return $candidate;
            }
            $nextSeq++;
        } while (true);
    }
}
