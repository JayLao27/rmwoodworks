<?php

namespace App\Http\Controllers;

use App\Models\Accounting;
use App\Models\InventoryMovement;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\WorkOrder;
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

        // For models that primarily use updated_at for their "last action"
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
            ->map(function ($item) {
                $action = $item->movement_type === 'in' ? 'Stock In' : 'Stock Out';
                $color = $item->movement_type === 'in' ? 'emerald' : 'orange';

                // If it's a production completion, call it "Completed"
                if ($item->movement_type === 'in' && str_contains($item->reference_type ?? '', 'WorkOrder')) {
                    $action = 'Completed';
                }

                return [
                    'type' => 'Inventory',
                    'action' => $action,
                    'description' => ($item->item->name ?? $item->item->product_name ?? 'Unknown Item') . ' (' . $item->quantity . ') - ' . $item->notes,
                    'user' => $item->user->name ?? 'System',
                    'user_role' => $item->user->role ?? 'system',
                    'date' => $item->created_at,
                    'status' => $item->status,
                    'color' => $color
                ];
            });

        $sales = $filterUpdatedDates(SalesOrder::with(['user', 'customer'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Sales',
                    'action' => 'Order ' . $item->status,
                    'description' => 'Order #' . $item->order_number . ' for ' . ($item->customer->name ?? 'Unknown Customer'),
                    'user' => $item->user->name ?? 'System',
                    'user_role' => $item->user->role ?? 'system',
                    'date' => $item->updated_at,
                    'status' => $item->status,
                    'color' => 'indigo'
                ];
            });

        $accounting = $filterDates(Accounting::with(['user', 'salesOrder', 'purchaseOrder'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Accounting',
                    'action' => $item->transaction_type === 'Income' ? 'Payment Received' : 'Payment Made',
                    'description' => 'Amount: P' . number_format($item->amount, 2) . ' - ' . $item->description,
                    'user' => $item->user->name ?? 'System',
                    'user_role' => $item->user->role ?? 'system',
                    'date' => $item->created_at,
                    'status' => 'Completed',
                    'color' => $item->transaction_type === 'Income' ? 'emerald' : 'red'
                ];
            });

        $production = $filterUpdatedDates(WorkOrder::with(['user', 'product'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Production',
                    'action' => 'Work Order ' . str_replace('_', ' ', $item->status),
                    'description' => 'WO #' . $item->order_number . ' for ' . ($item->product->product_name ?? 'Unknown Product'),
                    'user' => $item->user->name ?? 'System',
                    'user_role' => $item->user->role ?? 'system',
                    'date' => $item->updated_at,
                    'status' => $item->status,
                    'color' => 'purple'
                ];
            });

        $procurement = $filterUpdatedDates(PurchaseOrder::with(['user', 'supplier'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'Procurement',
                    'action' => 'Purchase Order ' . $item->status,
                    'description' => 'PO #' . $item->order_number . ' from ' . ($item->supplier->name ?? 'Unknown Supplier'),
                    'user' => $item->user->name ?? 'System',
                    'user_role' => $item->user->role ?? 'system',
                    'date' => $item->updated_at,
                    'status' => $item->status,
                    'color' => 'blue'
                ];
            });

        $systemActivities = $filterDates(\App\Models\SystemActivity::with(['user'])->latest())
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => $item->type,
                    'action' => $item->action,
                    'description' => $item->description,
                    'user' => $item->user->name ?? 'System',
                    'user_role' => $item->user->role ?? 'system',
                    'date' => $item->created_at,
                    'status' => 'Logged',
                    'color' => $item->color ?? 'slate'
                ];
            });

        // Combine and sort by date desc
        $allActivities = $inventory->concat($sales)
            ->concat($accounting)
            ->concat($production)
            ->concat($procurement)
            ->concat($systemActivities)
            ->sortByDesc('date');

        // Get unique roles for filtering (excluding admin)
        $availableRoles = \App\Models\User::where('role', '!=', 'admin')
            ->distinct()
            ->pluck('role')
            ->toArray();

        // Apply filtering if role is selected
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
