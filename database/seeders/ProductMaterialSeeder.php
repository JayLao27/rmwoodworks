<?php

namespace Database\Seeders;

use App\Models\Inventory\Product;
use App\Models\Inventory\Material;
use Illuminate\Database\Seeder;

class ProductMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all materials
        $mahogany = Material::where('name', 'Mahogany / Gmelina')->first();
        $narra = Material::where('name', 'Narra')->first();
        $lacquer = Material::where('name', 'Lacquer 5-star brand')->first();
        $sanding = Material::where('name', 'Sanding 5-star brand')->first();
        $topcoat = Material::where('name', 'Topcoat')->first();
        $reducer = Material::where('name', 'Reducer')->first();
        $nails2inch = Material::where('name', '2-inch Nails (1/4)')->first();
        $nails15inch = Material::where('name', '1.5-inch Nails (1/2)')->first();
        $sandpaper60 = Material::where('name', '3M #60 Sandpaper')->first();
        $sandpaper120 = Material::where('name', '#120 Sandpaper')->first();
        $epoxy = Material::where('name', 'Non-Sag Epoxy')->first();
        $calciumine = Material::where('name', 'Calciumine')->first();
        $roller = Material::where('name', 'Roller (1.5 meter)')->first();

        // Sala Set (Simple Design) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Sala Set (Simple Design) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Sala Set (Simple Design) - Narra
        $product = Product::where('product_name', 'Sala Set (Simple Design) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Sala Set (Curved Design) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Sala Set (Curved Design) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 280],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Sala Set (Curved Design) - Narra
        $product = Product::where('product_name', 'Sala Set (Curved Design) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 280],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Dining Set (4 Seaters) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Dining Set (4 Seaters) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 240],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Dining Set (6 Seaters) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Dining Set (6 Seaters) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 280],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Dining Set (4 Seaters) - Narra
        $product = Product::where('product_name', 'Dining Set (4 Seaters) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 240],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Dining Set (6 Seaters) - Narra
        $product = Product::where('product_name', 'Dining Set (6 Seaters) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 280],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 4],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Hanging Cabinet (7 feet) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Hanging Cabinet (7 feet) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 180],
                $lacquer->id => ['quantity_needed' => 1.5],
                $sanding->id => ['quantity_needed' => 1.5],
                $topcoat->id => ['quantity_needed' => 0.5],
                $reducer->id => ['quantity_needed' => 0.5],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.25],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 3],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Hanging Cabinet (7 feet) - Narra
        $product = Product::where('product_name', 'Hanging Cabinet (7 feet) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 180],
                $lacquer->id => ['quantity_needed' => 1.5],
                $sanding->id => ['quantity_needed' => 1.5],
                $topcoat->id => ['quantity_needed' => 0.5],
                $reducer->id => ['quantity_needed' => 0.5],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.25],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 3],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // King Size Bed (With Drawer) - Mahogany/Gmelina
        $product = Product::where('product_name', 'King Size Bed (With Drawer) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 220],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // King Size Bed - Narra
        $product = Product::where('product_name', 'King Size Bed - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 220],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // King Size Bed (With Drawer) - Narra
        $product = Product::where('product_name', 'King Size Bed (With Drawer) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 220],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Queen Size Bed (With Drawer) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Queen Size Bed (With Drawer) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 200],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Queen Size Bed - Mahogany/Gmelina
        $product = Product::where('product_name', 'Queen Size Bed - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 200],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Queen Size Bed (With Drawer) - Narra
        $product = Product::where('product_name', 'Queen Size Bed (With Drawer) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 200],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Queen Size Bed - Narra
        $product = Product::where('product_name', 'Queen Size Bed - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 200],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Cabinet (Wardrobe Design) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Cabinet (Wardrobe Design) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.5],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Cabinet (Wardrobe Design) - Narra
        $product = Product::where('product_name', 'Cabinet (Wardrobe Design) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.5],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Sliding Door Cabinet (Wardrobe) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Sliding Door Cabinet (Wardrobe) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.5],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
                $roller->id => ['quantity_needed' => 1.5],
            ]);
        }

        // Sliding Door Cabinet (Wardrobe) - Narra
        $product = Product::where('product_name', 'Sliding Door Cabinet (Wardrobe) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.5],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
                $roller->id => ['quantity_needed' => 1.5],
            ]);
        }

        // Door (Simple Design) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Door (Simple Design) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 60],
                $lacquer->id => ['quantity_needed' => 1],
                $sanding->id => ['quantity_needed' => 1],
                $topcoat->id => ['quantity_needed' => 0.5],
                $reducer->id => ['quantity_needed' => 0.5],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.25],
                $sandpaper60->id => ['quantity_needed' => 0.5],
                $sandpaper120->id => ['quantity_needed' => 3],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 0.5],
            ]);
        }

        // Door (Simple Design) - Narra
        $product = Product::where('product_name', 'Door (Simple Design) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 60],
                $lacquer->id => ['quantity_needed' => 1],
                $sanding->id => ['quantity_needed' => 1],
                $topcoat->id => ['quantity_needed' => 0.5],
                $reducer->id => ['quantity_needed' => 0.5],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.25],
                $sandpaper60->id => ['quantity_needed' => 0.5],
                $sandpaper120->id => ['quantity_needed' => 3],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 0.5],
            ]);
        }

        // Door (With Design) - Mahogany/Gmelina
        $product = Product::where('product_name', 'Door (With Design) - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 70],
                $lacquer->id => ['quantity_needed' => 1],
                $sanding->id => ['quantity_needed' => 1],
                $topcoat->id => ['quantity_needed' => 0.5],
                $reducer->id => ['quantity_needed' => 0.5],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.25],
                $sandpaper60->id => ['quantity_needed' => 0.5],
                $sandpaper120->id => ['quantity_needed' => 3],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 0.5],
            ]);
        }

        // Door (With Design) - Narra
        $product = Product::where('product_name', 'Door (With Design) - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 70],
                $lacquer->id => ['quantity_needed' => 1],
                $sanding->id => ['quantity_needed' => 1],
                $topcoat->id => ['quantity_needed' => 0.5],
                $reducer->id => ['quantity_needed' => 0.5],
                $nails2inch->id => ['quantity_needed' => 0.25],
                $nails15inch->id => ['quantity_needed' => 0.25],
                $sandpaper60->id => ['quantity_needed' => 0.5],
                $sandpaper120->id => ['quantity_needed' => 3],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 0.5],
            ]);
        }

        // Divider - Mahogany/Gmelina
        $product = Product::where('product_name', 'Divider - Mahogany/Gmelina')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $mahogany->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.5],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }

        // Divider - Narra
        $product = Product::where('product_name', 'Divider - Narra')->first();
        if ($product) {
            $product->materials()->detach();
            $product->materials()->attach([
                $narra->id => ['quantity_needed' => 250],
                $lacquer->id => ['quantity_needed' => 2],
                $sanding->id => ['quantity_needed' => 2],
                $topcoat->id => ['quantity_needed' => 1],
                $reducer->id => ['quantity_needed' => 1],
                $nails2inch->id => ['quantity_needed' => 0.5],
                $nails15inch->id => ['quantity_needed' => 0.5],
                $sandpaper60->id => ['quantity_needed' => 1],
                $sandpaper120->id => ['quantity_needed' => 5],
                $epoxy->id => ['quantity_needed' => 1],
                $calciumine->id => ['quantity_needed' => 1],
            ]);
        }
    }
}