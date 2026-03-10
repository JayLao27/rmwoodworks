<?php

namespace Tests\Feature;

use App\Models\System\User;
use App\Models\Inventory\Material;
use App\Models\Inventory\InventoryMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_movement_records_user_id()
    {
        // 1. Setup: Create a user and a material
        $user = User::factory()->create(['role' => 'admin']);
        $material = Material::factory()->create([
            'current_stock' => 10,
            'unit_cost' => 100,
        ]);

        // 2. Act: Adjust stock as the user
        $response = $this->actingAs($user)
            ->post(route('inventory.adjust-stock', $material->id), [
                'type' => 'material',
                'movement_type' => 'in',
                'quantity' => 5,
                'notes' => 'Test adjustment'
            ]);

        $response->assertStatus(302);

        // 3. Assert: Check if the movement has the correct user_id
        $this->assertDatabaseHas('inventory_movements', [
            'item_id' => $material->id,
            'item_type' => 'material',
            'movement_type' => 'in',
            'quantity' => 5,
            'user_id' => $user->id,
            'reference_type' => 'manual_adjustment'
        ]);
        
        $movement = InventoryMovement::first();
        $this->assertEquals($user->id, $movement->user_id);
        $this->assertEquals($user->name, $movement->user->name);
    }

    public function test_initial_stock_records_user_id()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $supplier = \App\Models\Procurement\Supplier::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('inventory.store'), [
                'type' => 'material',
                'name' => 'Oak Wood',
                'category' => 'Raw Material',
                'unit' => 'pcs',
                'current_stock' => 50,
                'minimum_stock' => 10,
                'unit_cost' => 500,
                'supplier_id' => $supplier->id
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('inventory_movements', [
            'movement_type' => 'in',
            'quantity' => 50,
            'user_id' => $user->id,
            'reference_type' => 'initial_stock'
        ]);
    }
}
