<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            
            // Foreign Keys
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->foreignId('shape_id')->nullable()->constrained('shapes')->onDelete('set null');
            
            // Product Details
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->decimal('weight', 8, 2)->nullable(); // in grams or carats
            $table->string('weight_unit')->default('grams'); // grams, carats, etc.
            $table->string('purity')->nullable(); // e.g., 24K, 18K, 92.5%, etc.
            
            // Images
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
