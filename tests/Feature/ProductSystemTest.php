<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductSystemTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $headers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->headers = ['Authorization' => 'Bearer ' . $this->user->createToken('test')->plainTextToken];
    }

    // --- Category Tests ---

    public function test_can_list_categories()
    {
        Category::create(['name' => 'Rings', 'slug' => 'rings']);
        Category::create(['name' => 'Necklaces', 'slug' => 'necklaces']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_create_category()
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Bracelets',
            'description' => 'Wrist wear',
            'is_active' => true
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Bracelets')
            ->assertJsonPath('data.slug', 'bracelets'); // Auto-slug check

        $this->assertDatabaseHas('categories', ['name' => 'Bracelets']);
    }

    // --- Product Type Tests ---

    public function test_can_create_product_type()
    {
        $response = $this->postJson('/api/product-types', [
            'name' => 'Jewelry',
            'description' => 'Fine jewelry',
            'is_active' => true
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Jewelry');
    }

    // --- Product Tests ---

    public function test_can_create_product_with_images()
    {
        Storage::fake('public');

        $category = Category::create(['name' => 'Rings', 'slug' => 'rings']);
        $type = ProductType::create(['name' => 'Jewelry']);

        $file = UploadedFile::fake()->create('ring.jpg', 100);

        $response = $this->postJson('/api/products', [
            'name' => 'Diamond Ring',
            'sku' => 'DR-001',
            'price' => 999.99,
            'category_id' => $category->id,
            'product_type_id' => $type->id,
            'image_1' => $file,
            'is_active' => true
        ], $this->headers);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Diamond Ring');

        $this->assertDatabaseHas('products', ['sku' => 'DR-001']);
        
        // Verify image storage
        $path = $response->json('data.image_1');
        Storage::disk('public')->assertExists($path);
    }

    public function test_can_list_products_with_filters()
    {
        $category1 = Category::create(['name' => 'Rings', 'slug' => 'rings']);
        $category2 = Category::create(['name' => 'Necklaces', 'slug' => 'necklaces']);
        $type = ProductType::create(['name' => 'Jewelry']);

        Product::create([
            'name' => 'Ring 1', 'sku' => 'R1', 'price' => 100,
            'category_id' => $category1->id, 'product_type_id' => $type->id
        ]);

        Product::create([
            'name' => 'Necklace 1', 'sku' => 'N1', 'price' => 200,
            'category_id' => $category2->id, 'product_type_id' => $type->id
        ]);

        // Filter by category
        $response = $this->getJson('/api/products?category_id=' . $category1->id);
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.name', 'Ring 1');
    }

    public function test_can_search_products()
    {
        $category = Category::create(['name' => 'Rings', 'slug' => 'rings']);
        $type = ProductType::create(['name' => 'Jewelry']);

        Product::create([
            'name' => 'Gold Ring', 'sku' => 'GR-001', 'price' => 100,
            'category_id' => $category->id, 'product_type_id' => $type->id
        ]);

        Product::create([
            'name' => 'Silver Ring', 'sku' => 'SR-001', 'price' => 50,
            'category_id' => $category->id, 'product_type_id' => $type->id
        ]);

        $response = $this->getJson('/api/products?search=Gold');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.name', 'Gold Ring');
    }
}
