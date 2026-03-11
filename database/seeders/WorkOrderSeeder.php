<?php

namespace Database\Seeders;

use App\Models\Production\WorkOrder;
use App\Models\Sales\SalesOrder;
use App\Models\Inventory\Product;
use Illuminate\Database\Seeder;

class WorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salesOrders = SalesOrder::with('items.product')->get();
        // Fallback if no sales orders exist (should not happen if SalesOrderSeeder runs first)
        if ($salesOrders->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
        $priorities = ['low', 'medium', 'high'];

        // Create 50 Work Orders
        for ($i = 0; $i < 50; $i++) {
            // Pick a random Sales Order
            $so = $salesOrders->random();
            // Pick a random item from that order to base the work order on
            if ($so->items->isEmpty()) continue;
            
            $item = $so->items->random();
            $product = $item->product;

            $status = $statuses[array_rand($statuses)];
            
            // Logic for dates based on status
            $startDate = null;
            $dueDate = \Carbon\Carbon::parse($so->delivery_date)->subDays(rand(1, 5)); 

            if ($status !== 'pending') {
                $startDate = \Carbon\Carbon::parse($so->order_date)->addDays(rand(1, 3));
            }
            
            $completionQuantity = 0;
            if ($status === 'completed') {
                $completionQuantity = $item->quantity;
            } elseif ($status === 'in_progress') {
                $completionQuantity = rand(0, $item->quantity);
            }

            WorkOrder::create([
                'order_number' => 'WO-' . now()->year . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'sales_order_id' => $so->id,
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'quantity' => $item->quantity,
                'completion_quantity' => $completionQuantity,
                'status' => $status,
                'due_date' => $dueDate,
                'starting_date' => $startDate,
                'assigned_to' => ['John', 'Mike', 'Sarah', 'Alex'][rand(0, 3)],
                'priority' => $priorities[array_rand($priorities)],
                'notes' => 'Generated work order for ' . $product->product_name
            ]);
        }
    }
}