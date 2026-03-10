<?php

namespace Database\Factories;

use App\Models\Procurement\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Wood',
            'category' => $this->faker->randomElement(['Hardwood', 'Softwood', 'Plywood']),
            'unit' => $this->faker->randomElement(['board feet', 'pcs', 'kg']),
            'current_stock' => $this->faker->numberBetween(10, 100),
            'minimum_stock' => $this->faker->numberBetween(5, 20),
            'unit_cost' => $this->faker->randomFloat(2, 50, 500),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
