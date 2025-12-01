<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Shape;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_populate_database_correctly()
    {
        // Run the seeders
        $this->seed();

        // Verify Product Types (should be 3 from ProductSeeder)
        $this->assertDatabaseCount('product_types', 3);
        $this->assertDatabaseHas('product_types', ['name' => 'Gemstone']);

        // Verify Categories (should be 5 from ProductSeeder)
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseHas('categories', ['name' => 'Rings']);

        // Verify Colors (should be 5 from ProductSeeder)
        $this->assertDatabaseCount('colors', 5);
        $this->assertDatabaseHas('colors', ['name' => 'Ruby Red']);

        // Verify Shapes (should be 6 from ProductSeeder)
        $this->assertDatabaseCount('shapes', 6);
        $this->assertDatabaseHas('shapes', ['name' => 'Round']);

        // Verify Products (should be 50 from ProductDummySeeder)
        $this->assertDatabaseCount('products', 50);
        
        // Verify a random product has valid relationships
        $product = Product::first();
        $this->assertNotNull($product->productType);
        $this->assertNotNull($product->category);
        $this->assertNotNull($product->color);
        $this->assertNotNull($product->shape);
    }
}
