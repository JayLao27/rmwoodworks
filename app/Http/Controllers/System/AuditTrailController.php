<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;

use App\Models\Accounting\Accounting;
use App\Models\Inventory\InventoryMovement;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Sales\SalesOrder;
use App\Models\Production\WorkOrder;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $limit = 100;
        $selectedRole = $request->input('role');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $filterDates = function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            }
            return $query;
        };

        $filterUpdatedDates = function ($query) use ($dateFrom, $dateTo) {
            if ($dateFrom) {
                $query->whereDate('updated_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $query->whereDate('updated_at', '<=', $dateTo);
            }
            return $query;
        };

        $inventory = $filterDates(InventoryMovement::with(['user', 'item'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($inventoryMovement) {
                $action = $inventoryMovement->movement_type === 'in' ? 'Stock In' : 'Stock Out';
                $color = $inventoryMovement->movement_type === 'in' ? 'emerald' : 'orange';

                if ($inventoryMovement->movement_type === 'in' && str_contains($inventoryMovement->reference_type ?? '', 'WorkOrder')) {
                    $action = 'Completed';
                }

                return [
                    'type' => 'Inventory',
                    'action' => $action,
                    'description' => ($inventoryMovement->item->name ?? $inventoryMovement->item->product_name ?? 'Unknown Item') . ' (' . $inventoryMovement->quantity . ') - ' . $inventoryMovement->notes,
                    'user' => $inventoryMovement->user->name ?? 'Admin',
                    'user_role' => $inventoryMovement->user->role ?? 'admin',
                    'date' => $inventoryMovement->created_at,
                    'status' => $inventoryMovement->status,
                    'color' => $color
                ];
            });

        $sales = $filterUpdatedDates(SalesOrder::with(['user', 'customer'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($salesOrderRecord) {
                return [
                    'type' => 'Sales',
                    'action' => 'Order ' . $salesOrderRecord->status,
                    'description' => 'Order #' . $salesOrderRecord->order_number . ' for ' . ($salesOrderRecord->customer->name ?? 'Unknown Customer'),
                    'user' => $salesOrderRecord->user->name ?? 'Admin',
                    'user_role' => $salesOrderRecord->user->role ?? 'admin',
                    'date' => $salesOrderRecord->updated_at,
                    'status' => $salesOrderRecord->status,
                    'color' => 'indigo'
                ];
            });

        $accounting = $filterDates(Accounting::with(['user', 'salesOrder', 'purchaseOrder'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($accountingEntry) {
                return [
                    'type' => 'Accounting',
                    'action' => $accountingEntry->transaction_type === 'Income' ? 'Payment Received' : 'Payment Made',
                    'description' => 'Amount: P' . number_format($accountingEntry->amount, 2) . ' - ' . $accountingEntry->description,
                    'user' => $accountingEntry->user->name ?? 'Admin',
                    'user_role' => $accountingEntry->user->role ?? 'admin',
                    'date' => $accountingEntry->created_at,
                    'status' => 'Completed',
                    'color' => $accountingEntry->transaction_type === 'Income' ? 'emerald' : 'red'
                ];
            });

        $production = $filterUpdatedDates(WorkOrder::with(['user', 'product'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($workOrderRecord) {
                return [
                    'type' => 'Production',
                    'action' => 'Work Order ' . str_replace('_', ' ', $workOrderRecord->status),
                    'description' => 'WO #' . $workOrderRecord->order_number . ' for ' . ($workOrderRecord->product->product_name ?? 'Unknown Product'),
                    'user' => $workOrderRecord->user->name ?? 'Admin',
                    'user_role' => $workOrderRecord->user->role ?? 'admin',
                    'date' => $workOrderRecord->updated_at,
                    'status' => $workOrderRecord->status,
                    'color' => 'purple'
                ];
            });

        $procurement = $filterUpdatedDates(PurchaseOrder::with(['user', 'supplier'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($purchaseOrderRecord) {
                return [
                    'type' => 'Procurement',
                    'action' => 'Purchase Order ' . $purchaseOrderRecord->status,
                    'description' => 'PO #' . $purchaseOrderRecord->order_number . ' from ' . ($purchaseOrderRecord->supplier->name ?? 'Unknown Supplier'),
                    'user' => $purchaseOrderRecord->user->name ?? 'Admin',
                    'user_role' => $purchaseOrderRecord->user->role ?? 'admin',
                    'date' => $purchaseOrderRecord->updated_at,
                    'status' => $purchaseOrderRecord->status,
                    'color' => 'blue'
                ];
            });

        $systemActivities = $filterDates(\App\Models\System\SystemActivity::with(['user'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($systemActivity) {
                return [
                    'type' => $systemActivity->type,
                    'action' => $systemActivity->action,
                    'description' => $systemActivity->description,
                    'user' => $systemActivity->user->name ?? 'Admin',
                    'user_role' => $systemActivity->user->role ?? 'admin',
                    'date' => $systemActivity->created_at,
                    'status' => 'Logged',
                    'color' => $systemActivity->color ?? 'slate'
                ];
            });

        $allActivities = $inventory->concat($sales)
            ->concat($accounting)
            ->concat($production)
            ->concat($procurement)
            ->concat($systemActivities)
            ->sortByDesc('date');

        $availableRoles = \App\Models\System\User::where('role', '!=', 'admin')
            ->distinct()
            ->pluck('role')
            ->toArray();

        if ($selectedRole) {
            $activities = $allActivities->filter(function ($activity) use ($selectedRole) {
                return $activity['user_role'] === $selectedRole;
            })->values();
        } else {
            $activities = $allActivities->values();
        }

        return view('Systems.audit-trails', compact('activities', 'availableRoles', 'selectedRole', 'dateFrom', 'dateTo'));
    }
}
