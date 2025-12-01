<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => strtoupper($this->faker->unique()->bothify('??-####')),
            'description' => $this->faker->paragraph(),
            'product_type_id' => \App\Models\ProductType::inRandomOrder()->first()->id ?? 1,
            'category_id' => \App\Models\Category::inRandomOrder()->first()->id ?? 1,
            'color_id' => \App\Models\Color::inRandomOrder()->first()->id ?? 1,
            'shape_id' => \App\Models\Shape::inRandomOrder()->first()->id ?? 1,
            'price' => $this->faker->randomFloat(2, 50, 5000),
            'stock' => $this->faker->numberBetween(0, 100),
            'weight' => $this->faker->randomFloat(2, 0.5, 10),
            'weight_unit' => 'grams',
            'purity' => $this->faker->randomElement(['18K', '22K', '24K', '925 Sterling', 'Platinum']),
            'is_active' => true,
            'is_featured' => $this->faker->boolean(20),
        ];
    }
}
