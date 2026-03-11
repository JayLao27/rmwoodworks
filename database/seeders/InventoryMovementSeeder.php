<?php

namespace Database\Seeders;

use App\Models\Inventory\InventoryMovement;
use Illuminate\Database\Seeder;

class InventoryMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Material inbound movements from purchase orders - using correct values for report query
        InventoryMovement::create([
            'item_type' => 'material',
            'item_id' => 1,
            'movement_type' => 'in',
            'quantity' => 500,
            'reference_type' => 'purchase_order',
            'reference_id' => 1,
            'notes' => 'Received oak lumber from Premium Oak Suppliers | Defect Qty: 0.00',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'material',
            'item_id' => 2,
            'movement_type' => 'in',
            'quantity' => 75,
            'reference_type' => 'purchase_order',
            'reference_id' => 2,
            'notes' => 'Partial delivery of maple plywood | Defect Qty: 5.00',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'material',
            'item_id' => 4,
            'movement_type' => 'in',
            'quantity' => 200,
            'reference_type' => 'purchase_order',
            'reference_id' => 4,
            'notes' => 'Pine lumber delivery received | Defect Qty: 2.50',
            'status' => 'completed',
        ]);

        // Product outbound movements from sales orders
        InventoryMovement::create([
            'item_type' => 'product',
            'item_id' => 1,
            'movement_type' => 'out',
            'quantity' => 2,
            'reference_type' => 'sales_order',
            'reference_id' => 1,
            'notes' => 'Sold to ABC Furniture Co.',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'product',
            'item_id' => 2,
            'movement_type' => 'out',
            'quantity' => 3,
            'reference_type' => 'sales_order',
            'reference_id' => 2,
            'notes' => 'Maple cabinets delivered to XYZ Interior Design',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'product',
            'item_id' => 3,
            'movement_type' => 'out',
            'quantity' => 6,
            'reference_type' => 'sales_order',
            'reference_id' => 1,
            'notes' => 'Walnut coffee tables - bulk order',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'product',
            'item_id' => 4,
            'movement_type' => 'out',
            'quantity' => 3,
            'reference_type' => 'sales_order',
            'reference_id' => 3,
            'notes' => 'Pine bookshelves to Local Woodcraft Store',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'product',
            'item_id' => 5,
            'movement_type' => 'out',
            'quantity' => 6,
            'reference_type' => 'sales_order',
            'reference_id' => 4,
            'notes' => 'Cherry wood doors for housing development',
            'status' => 'pending',
        ]);

        // Material adjustment movements
        InventoryMovement::create([
            'item_type' => 'material',
            'item_id' => 3,
            'movement_type' => 'adjustment',
            'quantity' => 10,
            'reference_type' => 'inventory_adjustment',
            'reference_id' => 1,
            'notes' => 'Inventory count adjustment - shrinkage',
            'status' => 'completed',
        ]);

        InventoryMovement::create([
            'item_type' => 'product',
            'item_id' => 2,
            'movement_type' => 'adjustment',
            'quantity' => 2,
            'reference_type' => 'inventory_adjustment',
            'reference_id' => 2,
            'notes' => 'Damaged units removed from stock',
            'status' => 'completed',
        ]);

        // Internal transfer
        InventoryMovement::create([
            'item_type' => 'material',
            'item_id' => 6,
            'movement_type' => 'internal_transfer',
            'quantity' => 5,
            'reference_type' => 'internal_transfer',
            'reference_id' => 1,
            'notes' => 'Transferred stain from warehouse A to warehouse B',
            'status' => 'completed',
        ]);
    }
}