<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\Product;
use App\Models\InventoryMovement;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // Create suppliers
        $supplier1 = Supplier::create([
            'name' => 'Premium Wood Supply',
            'contact_person' => 'John Smith',
            'phone' => '+63 912 345 6789',
            'email' => 'john@premiumwood.com',
            'address' => '123 Wood Street, Manila',
            'payment_terms' => 'Net 30',
            'status' => 'active'
        ]);

        $supplier2 = Supplier::create([
            'name' => 'Hardware Solutions',
            'contact_person' => 'Maria Garcia',
            'phone' => '+63 917 654 3210',
            'email' => 'maria@hardwaresolutions.com',
            'address' => '456 Hardware Ave, Quezon City',
            'payment_terms' => 'Net 15',
            'status' => 'active'
        ]);

        $supplier3 = Supplier::create([
            'name' => 'Finishing Solutions',
            'contact_person' => 'Carlos Rodriguez',
            'phone' => '+63 918 765 4321',
            'email' => 'carlos@finishingsolutions.com',
            'address' => '789 Finish Blvd, Makati',
            'payment_terms' => 'Net 30',
            'status' => 'active'
        ]);

        // Create materials
        $materials = [
            [
                'name' => 'Oak Lumber 2x4',
                'category' => 'Wood',
                'unit' => 'pieces',
                'current_stock' => 150,
                'minimum_stock' => 50,
                'unit_cost' => 699.00,
                'supplier_id' => $supplier1->id
            ],
            [
                'name' => 'Pine Lumber 2x6',
                'category' => 'Wood',
                'unit' => 'pieces',
                'current_stock' => 200,
                'minimum_stock' => 75,
                'unit_cost' => 899.00,
                'supplier_id' => $supplier1->id
            ],
            [
                'name' => 'Wood Screws 2"',
                'category' => 'Hardware',
                'unit' => 'pieces',
                'current_stock' => 850,
                'minimum_stock' => 1000,
                'unit_cost' => 9.00,
                'supplier_id' => $supplier2->id
            ],
            [
                'name' => 'Wood Glue',
                'category' => 'Adhesive',
                'unit' => 'bottles',
                'current_stock' => 25,
                'minimum_stock' => 10,
                'unit_cost' => 499.00,
                'supplier_id' => $supplier2->id
            ],
            [
                'name' => 'Wood Stain - Oak',
                'category' => 'Finishing',
                'unit' => 'liters',
                'current_stock' => 4,
                'minimum_stock' => 5,
                'unit_cost' => 1499.00,
                'supplier_id' => $supplier3->id
            ]
        ];

        foreach ($materials as $materialData) {
            $material = Material::create($materialData);
            
            // Create initial inventory movement
            InventoryMovement::create([
                'item_type' => 'material',
                'item_id' => $material->id,
                'movement_type' => 'in',
                'quantity' => $materialData['current_stock'],
                'reference_type' => 'initial_stock',
                'notes' => 'Initial stock entry'
            ]);
        }

    }
}