<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\WorkOrder;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with(['inventoryMovements', 'materials'])->orderBy('product_name')->get();
        $materials = Material::with(['supplier', 'inventoryMovements'])->orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        
        // Calculate metrics
        $totalMaterials = $materials->count();
        $lowStockAlerts = $materials->filter(function ($material) {
            return $material->current_stock <= $material->minimum_stock;
        })->count();
        $totalValue = $materials->sum(function($material) {
            return $material->current_stock * $material->unit_cost;
        });
        $newOrders = \App\Models\SalesOrder::where('status', 'Pending')->count();
        $pendingDeliveries = \App\Models\PurchaseOrder::whereIn('status', ['Pending', 'Partial'])->count();
        
        return view('Systems.inventory', compact(
            'products', 'materials', 'suppliers', 
            'lowStockAlerts', 'totalValue', 'newOrders', 'pendingDeliveries'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:product,material',
            'name' => 'required_if:type,material|string|max:255',
            'product_name' => 'required_if:type,product|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required_if:type,material|numeric|min:0',
            'minimum_stock' => 'required_if:type,material|numeric|min:0',
            'unit_cost' => 'required_if:type,material|numeric|min:0',
            'supplier_id' => 'required_if:type,material|exists:suppliers,id',
            'selling_price' => 'required_if:type,product|numeric|min:0',
            'production_cost' => 'required_if:type,product|numeric|min:0',
            'materials' => 'nullable|array',
            'materials.*.material_id' => 'required_with:materials|exists:materials,id',
            'materials.*.quantity_needed' => 'required_with:materials|numeric|min:0.01'
        ]);

        if ($request->type === 'product') {
            $item = Product::create([
                'product_name' => $request->product_name,
                'category' => $request->category,
                'unit' => $request->unit,
                'current_stock' => 0,
                'minimum_stock' => 0,
                'selling_price' => $request->selling_price,
                'production_cost' => $request->production_cost,
            ]);

            // Sync materials to product if provided
            if ($request->has('materials') && is_array($request->materials)) {
                $materials = collect($request->materials)
                    ->filter(fn($m) => !empty($m['material_id']))
                    ->groupBy('material_id')
                    ->map(fn($group) => ['quantity_needed' => $group->sum('quantity_needed')])
                    ->toArray();
                
                $item->materials()->sync($materials);
            }
            \App\Models\SystemActivity::log('Inventory', 'Product Created', "New product '{$item->product_name}' added to inventory.", 'indigo');
        } else {
            $item = Material::create([
                'name' => $request->name,
                'category' => $request->category,
                'unit' => $request->unit,
                'current_stock' => $request->current_stock,
                'minimum_stock' => $request->minimum_stock,
                'unit_cost' => $request->unit_cost,
                'supplier_id' => $request->supplier_id
            ]);
            \App\Models\SystemActivity::log('Inventory', 'Material Created', "New material '{$item->name}' added to inventory.", 'amber');
        }

        // Create initial inventory movement only for materials
        if ($request->type === 'material') {
            InventoryMovement::create([
                'item_type' => $request->type,
                'item_id' => $item->id,
                'movement_type' => 'in',
                'quantity' => $request->current_stock,
                'reference_type' => 'initial_stock',
                'notes' => 'Initial stock entry',
                'user_id' => auth()->id()
            ]);
        }

        $itemType = $request->type === 'product' ? 'Product' : 'Material';
        
        // Clear cache
        \App\Services\CacheService::clearRelated($request->type);
        
        return redirect()->route('inventory')->with('success', $itemType . ' added successfully!');
    }

    public function editProduct($id)
    {
        $product = Product::with('materials')->findOrFail($id);
        
        return response()->json([
            'product_name' => $product->product_name,
            'description' => $product->description,
            'category' => $product->category,
            'unit' => $product->unit,
            'production_cost' => $product->production_cost,
            'selling_price' => $product->selling_price,
            'materials' => $product->materials->map(function($material) {
                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'unit' => $material->unit,
                    'pivot' => [
                        'quantity_needed' => $material->pivot->quantity_needed
                    ]
                ];
            })
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:product,material',
            'name' => 'required_if:type,material|string|max:255',
            'product_name' => 'required_if:type,product|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required_if:type,material|numeric|min:0',
            'minimum_stock' => 'required_if:type,material|numeric|min:0',
            'unit_cost' => 'required_if:type,material|numeric|min:0',
            'supplier_id' => 'required_if:type,material|exists:suppliers,id',
            'selling_price' => 'required_if:type,product|numeric|min:0',
            'production_cost' => 'required_if:type,product|numeric|min:0',
            'materials' => 'nullable|array',
            'materials.*.material_id' => 'required_with:materials|exists:materials,id',
            'materials.*.quantity_needed' => 'required_with:materials|numeric|min:0.01'
        ]);

        if ($request->type === 'product') {
            $item = Product::findOrFail($id);
            $item->update([
                'product_name' => $request->product_name,
                'description' => $request->description,
                'category' => $request->category,
                'unit' => $request->unit,
                'selling_price' => $request->selling_price,
                'production_cost' => $request->production_cost
            ]);

            // Update materials
            // Sync materials
            if ($request->has('materials') && is_array($request->materials)) {
                $materials = collect($request->materials)
                    ->filter(fn($m) => !empty($m['material_id']))
                    ->groupBy('material_id')
                    ->map(fn($group) => ['quantity_needed' => $group->sum('quantity_needed')])
                    ->toArray();
                
                $item->materials()->sync($materials);
            } else {
                $item->materials()->detach();
            }
            \App\Models\SystemActivity::log('Inventory', 'Product Updated', "Product '{$item->product_name}' details updated.", 'indigo');
        } else {
            $item = Material::findOrFail($id);
            $item->update([
                'name' => $request->name,
                'category' => $request->category,
                'unit' => $request->unit,
                'current_stock' => $request->current_stock,
                'minimum_stock' => $request->minimum_stock,
                'unit_cost' => $request->unit_cost,
                'supplier_id' => $request->supplier_id
            ]);
            \App\Models\SystemActivity::log('Inventory', 'Material Updated', "Material '{$item->name}' details updated.", 'amber');
        }

        $itemType = $request->type === 'product' ? 'Product' : 'Material';
        
        // Clear cache
        \App\Services\CacheService::clearRelated($request->type);
        
        return redirect()->route('inventory')->with('success', $itemType . ' updated successfully!');
    }

    public function destroy($id, $type)
    {
        if ($type === 'product') {
            $item = Product::findOrFail($id);
            
            // Check for active sales orders
            $activeOrdersCount = \App\Models\SalesOrderItem::where('product_id', $id)
                ->whereHas('salesOrder', function($query) {
                    $query->whereNotIn('status', ['Cancelled', 'Delivered']);
                })
                ->count();

            if ($activeOrdersCount > 0) {
                return redirect()->back()->with('error', 'Cannot delete item associated with active orders');
            }
        } else {
            $item = Material::findOrFail($id);
        }

        $itemData = $type === 'material' ? $item->name : $item->product_name;
        $item->delete();

        // Clear cache
        \App\Services\CacheService::clearRelated($type);
        \App\Models\SystemActivity::log('Inventory', ucfirst($type) . ' Deleted', "{$type} '{$itemData}' removed from system.", 'red');

        $itemType = $type === 'product' ? 'Product' : 'Material';
        return redirect()->route('inventory')->with('success', $itemType . ' deleted successfully!');
    }

    public function getDetails(Request $request, $id)
    {
        $type = $request->query('type');

        if ($type === 'product') {
            $item = Product::with('materials')->findOrFail($id);
            $cost = $item->production_cost; // For products, show production cost
            $materials = $item->materials->map(function ($material) {
                return [
                    'name' => $material->name,
                    'unit' => $material->unit,
                    'quantity_needed' => $material->pivot->quantity_needed,
                ];
            })->values();
            $movements = [];
        } else {
            $item = Material::with(['inventoryMovements.user'])->findOrFail($id);
            $cost = $item->unit_cost; // For materials, show unit cost
            $materials = [];
            $movements = $item->inventoryMovements
                ->sortByDesc('created_at')
                ->map(function($movement) {
                    return [
                        'movement_type' => $movement->movement_type,
                        'quantity' => $movement->quantity,
                        'reference_type' => $movement->reference_type,
                        'reference_id' => $movement->reference_id,
                        'notes' => $movement->notes,
                        'created_at' => $movement->created_at,
                        'user_name' => $movement->user->name ?? 'System',
                    ];
                })
                ->values();
        }

        return response()->json([
            'item' => [
                'name' => $item->name ?? $item->product_name,
                'current_stock' => $item->current_stock,
                'minimum_stock' => $item->minimum_stock ?? 0,
                'unit_cost' => $cost ?? 0,
                'unit' => $item->unit,
                'production_cost' => $item->production_cost ?? null,
                'selling_price' => $item->selling_price ?? null,
            ],
            'materials' => $materials,
            'movements' => $movements
        ]);
    }

    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:product,material',
            'movement_type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        if ($request->type === 'product') {
            $item = Product::findOrFail($id);
        } else {
            $item = Material::findOrFail($id);
        }

        DB::transaction(function () use ($item, $request) {
            // Create inventory movement
            InventoryMovement::create([
                'item_type' => $request->type,
                'item_id' => $item->id,
                'movement_type' => $request->movement_type,
                'quantity' => $request->quantity,
                'reference_type' => 'manual_adjustment',
                'notes' => $request->notes,
                'user_id' => auth()->id()
            ]);

            // Update stock
            if ($request->movement_type === 'in') {
                $item->increment('current_stock', $request->quantity);
            } elseif ($request->movement_type === 'out') {
                $item->decrement('current_stock', $request->quantity);
            } else {
                $item->update(['current_stock' => $request->quantity]);
            }
        });

        $itemName = $request->type === 'material' ? $item->name : $item->product_name;
        \App\Models\SystemActivity::log('Inventory', 'Stock Adjusted', "Manual stock adjustment for '{$itemName}': {$request->quantity} units.", 'orange');

        return redirect()->route('inventory')->with('success', 'Stock adjusted successfully!');
    }

    /**
     * Get stock movements report - logs of material stock movements only
     */
    public function stockMovementsReport(Request $request)
    {
        $query = InventoryMovement::query()
            ->with(['item', 'user'])
            ->where('item_type', 'material') // Only get materials
            ->orderBy('created_at', 'desc');

        // Apply filters if provided
        if ($request->has('movement_type') && $request->movement_type) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->get();

        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereIn('id', $movements->where('reference_type', 'purchase_order')->pluck('reference_id')->unique())
            ->get()
            ->keyBy('id');

        $workOrders = WorkOrder::whereIn('id', $movements->where('reference_type', 'work_order')->pluck('reference_id')->unique())
            ->get()
            ->keyBy('id');

        // Format for response
        $formattedMovements = $movements->map(function ($movement) use ($purchaseOrders, $workOrders) {
            $itemName = $movement->item?->name ?? 'Unknown Material';
            $itemUnit = $movement->item?->unit ?? 'unit';

            // Determine movement label
            $movementLabel = match($movement->movement_type) {
                'in' => '📦 Stock In',
                'out' => '📤 Stock Out',
                'adjustment' => '⚙️ Adjustment',
                default => $movement->movement_type
            };

            // Get reference info
            $referenceLabel = match($movement->reference_type) {
                PurchaseOrder::class => 'Supplier',
                WorkOrder::class => 'Team Assigned',
                'manual_adjustment' => 'Manual Adjustment',
                'initial_stock' => 'Initial Stock',
                default => ucfirst(str_replace('_', ' ', (string) $movement->reference_type))
            };

            $referenceInfo = '';
            if ($movement->reference_type === 'purchase_order') {
                $po = $purchaseOrders->get($movement->reference_id);
                $referenceInfo = $po ? "PO: {$po->order_number}" : "PO #" . $movement->reference_id;
            } elseif ($movement->reference_type === 'work_order') {
                $wo = $workOrders->get($movement->reference_id);
                $referenceInfo = $wo ? $wo->order_number : "WO #" . $movement->reference_id;
            } elseif ($movement->reference_type === 'manual_adjustment') {
                $referenceInfo = "Manual Adjustment";
            } elseif ($movement->reference_type === 'initial_stock') {
                $referenceInfo = "Initial Stock";
            } else {
                $referenceInfo = ucfirst(str_replace('_', ' ', $movement->reference_type));
            }

            $purchaseOrder = $movement->reference_type === 'purchase_order'
                ? $purchaseOrders->get($movement->reference_id)
                : null;
            $supplierName = $purchaseOrder?->supplier?->name;
            $poNumber = $purchaseOrder?->order_number ? 'PO: ' . $purchaseOrder->order_number : null;

            $workOrder = $movement->reference_type === 'work_order'
                ? $workOrders->get($movement->reference_id)
                : null;
            $woId = $workOrder?->order_number;
            $teamAssigned = $workOrder?->assigned_to;

            return [
                'id' => $movement->id,
                'date' => $movement->created_at->format('Y-m-d'),
                'time' => $movement->created_at->format('H:i'),
                'movement_type' => $movement->movement_type,
                'movement_label' => $movementLabel,
                'item_type' => $movement->item_type,
                'item_name' => $itemName,
                'unit' => $itemUnit,
                'quantity' => (float) $movement->quantity,
                'reference_type' => $movement->reference_type,
                'reference_label' => $referenceLabel,
                'reference_info' => $referenceInfo,
                'supplier_name' => $supplierName,
                'po_number' => $poNumber,
                'wo_id' => $woId,
                'team_assigned' => $teamAssigned,
                'notes' => $movement->notes,
                'user_name' => $movement->user->name ?? 'System',
                'status' => $movement->status ?? 'completed',
                'created_at' => $movement->created_at
            ];
        });

        // Calculate summary
        $totalIn = $movements->where('movement_type', 'in')->sum('quantity');
        $totalOut = $movements->where('movement_type', 'out')->sum('quantity');

        return response()->json([
            'success' => true,
            'movements' => $formattedMovements,
            'summary' => [
                'total_in' => (float) $totalIn,
                'total_out' => (float) $totalOut,
                'total_movements' => count($formattedMovements)
            ]
        ]);
    }
}
