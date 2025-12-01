<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\ProductType;
use App\Models\Shape;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Product Types
        $types = ['Gemstone', 'Jewelry', 'Accessory'];
        foreach ($types as $type) {
            ProductType::firstOrCreate(['name' => $type], ['description' => "$type items"]);
        }

        // Categories
        $categories = ['Rings', 'Necklaces', 'Earrings', 'Bracelets', 'Loose Stones'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat], ['slug' => \Illuminate\Support\Str::slug($cat)]);
        }

        // Colors
        $colors = [
            ['name' => 'Ruby Red', 'hex' => '#E0115F'],
            ['name' => 'Sapphire Blue', 'hex' => '#0F52BA'],
            ['name' => 'Emerald Green', 'hex' => '#50C878'],
            ['name' => 'Diamond White', 'hex' => '#FFFFFF'],
            ['name' => 'Gold', 'hex' => '#FFD700'],
        ];
        foreach ($colors as $color) {
            Color::firstOrCreate(['name' => $color['name']], ['hex_code' => $color['hex']]);
        }

        // Shapes
        $shapes = ['Round', 'Oval', 'Princess', 'Emerald', 'Marquise', 'Pear'];
        foreach ($shapes as $shape) {
            Shape::firstOrCreate(['name' => $shape], ['description' => "$shape shape"]);
        }
    }
}
