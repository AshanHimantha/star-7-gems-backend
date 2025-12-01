<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'product_type_id',
        'category_id',
        'color_id',
        'shape_id',
        'price',
        'stock',
        'weight',
        'weight_unit',
        'purity',
        'image_1',
        'image_2',
        'image_3',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the product type
     */
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the color
     */
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Get the shape
     */
    public function shape()
    {
        return $this->belongsTo(Shape::class);
    }

    /**
     * Get all images as an array
     */
    public function getImagesAttribute()
    {
        return array_filter([
            $this->image_1,
            $this->image_2,
            $this->image_3,
        ]);
    }
}
