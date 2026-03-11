<?php

namespace Database\Seeders;

use App\Models\Inventory\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sala Set Products
        Product::create([
            'product_name' => 'Sala Set (Simple Design) - Mahogany/Gmelina',
            'description' => 'Beautiful simple design sala set made from Mahogany/Gmelina wood',
            'category' => 'furniture',
            'production_cost' => 4000.00,
            'selling_price' => 17000.00,
            'current_stock' => 5,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Sala Set (Simple Design) - Narra',
            'description' => 'Elegant simple design sala set made from premium Narra wood',
            'category' => 'furniture',
            'production_cost' => 7000.00,
            'selling_price' => 35000.00,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Sala Set (Curved Design) - Mahogany/Gmelina',
            'description' => 'Sophisticated curved design sala set in Mahogany/Gmelina',
            'category' => 'furniture',
            'production_cost' => 5000.00,
            'selling_price' => 25000.00,
            'current_stock' => 4,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Sala Set (Curved Design) - Narra',
            'description' => 'Premium curved design sala set made from Narra wood',
            'category' => 'furniture',
            'production_cost' => 8000.00,
            'selling_price' => 40000.00,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Dining Sets
        Product::create([
            'product_name' => 'Dining Set (4 Seaters) - Mahogany/Gmelina',
            'description' => '4-seater dining set in Mahogany/Gmelina wood',
            'category' => 'furniture',
            'production_cost' => 4000.00,
            'selling_price' => 15000.00,
            'current_stock' => 6,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Dining Set (6 Seaters) - Mahogany/Gmelina',
            'description' => '6-seater dining set in Mahogany/Gmelina wood',
            'category' => 'furniture',
            'production_cost' => 4000.00,
            'selling_price' => 18000.00,
            'current_stock' => 4,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Dining Set (4 Seaters) - Narra',
            'description' => '4-seater dining set in premium Narra wood',
            'category' => 'furniture',
            'production_cost' => 6000.00,
            'selling_price' => 20000.00,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Dining Set (6 Seaters) - Narra',
            'description' => '6-seater dining set in premium Narra wood',
            'category' => 'furniture',
            'production_cost' => 6000.00,
            'selling_price' => 30000.00,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Hanging Cabinets
        Product::create([
            'product_name' => 'Hanging Cabinet (7 feet) - Mahogany/Gmelina',
            'description' => '7-foot hanging cabinet in Mahogany/Gmelina',
            'category' => 'cabinets',
            'production_cost' => 3000.00,
            'selling_price' => 16000.00,
            'current_stock' => 5,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Hanging Cabinet (7 feet) - Narra',
            'description' => '7-foot hanging cabinet in premium Narra',
            'category' => 'cabinets',
            'production_cost' => 5000.00,
            'selling_price' => 34000.00,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Beds
        Product::create([
            'product_name' => 'King Size Bed (With Drawer) - Mahogany/Gmelina',
            'description' => 'King size bed with drawers in Mahogany/Gmelina',
            'category' => 'furniture',
            'production_cost' => 4000.00,
            'selling_price' => 20000.00,
            'current_stock' => 4,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'King Size Bed - Narra',
            'description' => 'Premium king size bed in Narra wood',
            'category' => 'furniture',
            'production_cost' => 7000.00,
            'selling_price' => 30000.00,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'King Size Bed (With Drawer) - Narra',
            'description' => 'Premium king size bed with drawers in Narra',
            'category' => 'furniture',
            'production_cost' => 7000.00,
            'selling_price' => 40000.00,
            'current_stock' => 1,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Queen Size Bed (With Drawer) - Mahogany/Gmelina',
            'description' => 'Queen size bed with drawers in Mahogany/Gmelina',
            'category' => 'furniture',
            'production_cost' => 4000.00,
            'selling_price' => 18000.00,
            'current_stock' => 5,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Queen Size Bed - Mahogany/Gmelina',
            'description' => 'Queen size bed in Mahogany/Gmelina wood',
            'category' => 'furniture',
            'production_cost' => 4000.00,
            'selling_price' => 16000.00,
            'current_stock' => 6,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Queen Size Bed (With Drawer) - Narra',
            'description' => 'Premium queen size bed with drawers in Narra',
            'category' => 'furniture',
            'production_cost' => 6000.00,
            'selling_price' => 35000.00,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Queen Size Bed - Narra',
            'description' => 'Premium queen size bed in Narra wood',
            'category' => 'furniture',
            'production_cost' => 6000.00,
            'selling_price' => 30000.00,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Wardrobes
        Product::create([
            'product_name' => 'Cabinet (Wardrobe Design) - Mahogany/Gmelina',
            'description' => 'Wardrobe cabinet in Mahogany/Gmelina design',
            'category' => 'cabinets',
            'production_cost' => 5000.00,
            'selling_price' => 20000.00,
            'current_stock' => 4,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Cabinet (Wardrobe Design) - Narra',
            'description' => 'Premium wardrobe cabinet in Narra design',
            'category' => 'cabinets',
            'production_cost' => 7000.00,
            'selling_price' => 40000.00,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Sliding Door Cabinets
        Product::create([
            'product_name' => 'Sliding Door Cabinet (Wardrobe) - Mahogany/Gmelina',
            'description' => 'Wardrobe with sliding doors in Mahogany/Gmelina',
            'category' => 'cabinets',
            'production_cost' => 5000.00,
            'selling_price' => 22000.00,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Sliding Door Cabinet (Wardrobe) - Narra',
            'description' => 'Premium wardrobe with sliding doors in Narra',
            'category' => 'cabinets',
            'production_cost' => 7000.00,
            'selling_price' => 42000.00,
            'current_stock' => 1,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Doors
        Product::create([
            'product_name' => 'Door (Simple Design) - Mahogany/Gmelina',
            'description' => 'Simple design door in Mahogany/Gmelina',
            'category' => 'doors',
            'production_cost' => 1500.00,
            'selling_price' => 6000.00,
            'current_stock' => 8,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Door (Simple Design) - Narra',
            'description' => 'Simple design door in premium Narra',
            'category' => 'doors',
            'production_cost' => 2000.00,
            'selling_price' => 12000.00,
            'current_stock' => 4,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Door (With Design) - Mahogany/Gmelina',
            'description' => 'Designer door in Mahogany/Gmelina',
            'category' => 'doors',
            'production_cost' => 2000.00,
            'selling_price' => 8000.00,
            'current_stock' => 6,
            'minimum_stock' => 2,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Door (With Design) - Narra',
            'description' => 'Premium designer door in Narra',
            'category' => 'doors',
            'production_cost' => 3000.00,
            'selling_price' => 18000.00,
            'current_stock' => 3,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        // Dividers
        Product::create([
            'product_name' => 'Divider - Mahogany/Gmelina',
            'description' => 'Room divider in Mahogany/Gmelina',
            'category' => 'furniture',
            'production_cost' => 5000.00,
            'selling_price' => 20000.00,
            'current_stock' => 4,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);

        Product::create([
            'product_name' => 'Divider - Narra',
            'description' => 'Premium room divider in Narra',
            'category' => 'furniture',
            'production_cost' => 7000.00,
            'selling_price' => 40000.00,
            'current_stock' => 2,
            'minimum_stock' => 1,
            'unit' => 'piece',
        ]);
    }
}