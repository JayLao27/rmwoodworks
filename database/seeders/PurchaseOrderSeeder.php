<?php

namespace Database\Seeders;

use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use App\Models\Inventory\Material;
use App\Models\Procurement\Supplier;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materials = Material::all();
        $suppliers = Supplier::all();

        if ($materials->isEmpty() || $suppliers->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'approved', 'received', 'cancelled'];
        
        for ($i = 0; $i < 50; $i++) {
            $supplier = $suppliers->random();
            $status = $statuses[array_rand($statuses)];
            
            $orderDate = Carbon::now()->subDays(rand(1, 60));
            $expectedDelivery = (clone $orderDate)->addDays(rand(5, 15));

            $po = PurchaseOrder::create([
                'order_number' => 'PO-' . now()->year . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $supplier->id,
                'order_date' => $orderDate,
                'expected_delivery' => $expectedDelivery,
                'total_amount' => 0, // Will update
                'paid_amount' => 0,
                'notes' => 'Random purchase order',
                'status' => $status,
                'assigned_to' => ['John', 'Mike', 'Sarah'][rand(0, 2)],
                'quality_status' => $status === 'received' ? 'approved' : 'pending',
                'approved_by' => in_array($status, ['approved', 'received']) ? 'Manager' : null,
                'approval_date' => in_array($status, ['approved', 'received']) ? now() : null,
                'updated_date' => now(),
            ]);

            $totalAmount = 0;
            $itemsCount = rand(1, 3);

            for ($j = 0; $j < $itemsCount; $j++) {
                $material = $materials->random();
                $quantity = rand(10, 100);
                $unitPrice = $material->unit_cost ?? 10.0; // Fallback if no price
                $totalPrice = $quantity * $unitPrice;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'material_id' => $material->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                $totalAmount += $totalPrice;
            }

            // Update PO totals
            $paidAmount = 0;
            if ($status === 'received') {
                $paidAmount = $totalAmount;
            } elseif ($status === 'approved') {
                $paidAmount = $totalAmount / 2;
            }

            $po->update([
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
            ]);
        }
    }
}