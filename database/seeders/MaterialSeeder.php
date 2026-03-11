<?php

namespace Database\Seeders;

use App\Models\Inventory\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wood Materials
        Material::create([
            'name' => 'Mahogany / Gmelina',
            'category' => 'wood',
            'unit' => 'board_ft',
            'current_stock' => 5000,
            'minimum_stock' => 500,
            'unit_cost' => 35.00,
            'supplier_id' => 1,
        ]);

        Material::create([
            'name' => 'Narra',
            'category' => 'wood',
            'unit' => 'board_ft',
            'current_stock' => 4000,
            'minimum_stock' => 400,
            'unit_cost' => 85.00,
            'supplier_id' => 1,
        ]);

        // Finishing Materials
        Material::create([
            'name' => 'Lacquer 5-star brand',
            'category' => 'finishing',
            'unit' => 'gallon',
            'current_stock' => 300,
            'minimum_stock' => 50,
            'unit_cost' => 415.00,
            'supplier_id' => 2,
        ]);

        Material::create([
            'name' => 'Sanding 5-star brand',
            'category' => 'finishing',
            'unit' => 'gallon',
            'current_stock' => 250,
            'minimum_stock' => 40,
            'unit_cost' => 650.00,
            'supplier_id' => 2,
        ]);

        Material::create([
            'name' => 'Topcoat',
            'category' => 'finishing',
            'unit' => 'liter',
            'current_stock' => 200,
            'minimum_stock' => 30,
            'unit_cost' => 585.00,
            'supplier_id' => 2,
        ]);

        Material::create([
            'name' => 'Reducer',
            'category' => 'finishing',
            'unit' => 'liter',
            'current_stock' => 200,
            'minimum_stock' => 30,
            'unit_cost' => 215.00,
            'supplier_id' => 2,
        ]);

        // Hardware
        Material::create([
            'name' => '2-inch Nails (1/4)',
            'category' => 'hardware',
            'unit' => 'pack',
            'current_stock' => 500,
            'minimum_stock' => 100,
            'unit_cost' => 25.00,
            'supplier_id' => 3,
        ]);

        Material::create([
            'name' => '1.5-inch Nails (1/2)',
            'category' => 'hardware',
            'unit' => 'pack',
            'current_stock' => 400,
            'minimum_stock' => 80,
            'unit_cost' => 40.00,
            'supplier_id' => 3,
        ]);

        Material::create([
            'name' => 'Roller (1.5 meter)',
            'category' => 'hardware',
            'unit' => 'piece',
            'current_stock' => 100,
            'minimum_stock' => 20,
            'unit_cost' => 2000.00,
            'supplier_id' => 3,
        ]);

        // Sandpaper
        Material::create([
            'name' => '3M #60 Sandpaper',
            'category' => 'abrasives',
            'unit' => 'piece',
            'current_stock' => 600,
            'minimum_stock' => 100,
            'unit_cost' => 75.00,
            'supplier_id' => 4,
        ]);

        Material::create([
            'name' => '#120 Sandpaper',
            'category' => 'abrasives',
            'unit' => 'piece',
            'current_stock' => 800,
            'minimum_stock' => 150,
            'unit_cost' => 25.00,
            'supplier_id' => 4,
        ]);

        // Adhesives & Chemicals
        Material::create([
            'name' => 'Non-Sag Epoxy',
            'category' => 'adhesive',
            'unit' => 'quart',
            'current_stock' => 150,
            'minimum_stock' => 30,
            'unit_cost' => 210.00,
            'supplier_id' => 2,
        ]);

        Material::create([
            'name' => 'Calciumine',
            'category' => 'finishing',
            'unit' => 'kilo',
            'current_stock' => 300,
            'minimum_stock' => 50,
            'unit_cost' => 20.00,
            'supplier_id' => 4,
        ]);
    }
}