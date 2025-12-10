<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Parameter(name="category_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="product_type_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="color_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="shape_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="is_featured", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="min_price", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Parameter(name="max_price", in="query", required=false, @OA\Schema(type="number")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = Product::with(['productType', 'category', 'color', 'shape']);

        // Filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('product_type_id')) {
            $query->where('product_type_id', $request->product_type_id);
        }

        if ($request->has('color_id')) {
            $query->where('color_id', $request->color_id);
        }

        if ($request->has('shape_id')) {
            $query->where('shape_id', $request->shape_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name", "sku", "product_type_id", "category_id", "price"},
     *                 @OA\Property(property="name", type="string", example="Blue Sapphire Ring"),
     *                 @OA\Property(property="sku", type="string", example="BSR-001"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="product_type_id", type="integer", example=1),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="color_id", type="integer", example=1),
     *                 @OA\Property(property="shape_id", type="integer", example=1),
     *                 @OA\Property(property="price", type="number", format="float", example=299.99),
     *                 @OA\Property(property="weight", type="number", format="float", example=2.5),
     *                 @OA\Property(property="weight_unit", type="string", example="carats"),
     *                 @OA\Property(property="purity", type="string", example="18K"),
     *                 @OA\Property(property="image_1", type="string", format="binary"),
     *                 @OA\Property(property="image_2", type="string", format="binary"),
     *                 @OA\Property(property="image_3", type="string", format="binary"),
     *                 @OA\Property(property="is_active", type="boolean"),
     *                 @OA\Property(property="is_featured", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'required|string|max:255|unique:products',
                'description' => 'nullable|string',
                'product_type_id' => 'required|exists:product_types,id',
                'category_id' => 'required|exists:categories,id',
                'color_id' => 'nullable|exists:colors,id',
                'shape_id' => 'nullable|exists:shapes,id',
                'price' => 'required|numeric|min:0',
                'weight' => 'nullable|numeric|min:0',
                'weight_unit' => 'nullable|string|max:50',
                'purity' => 'nullable|string|max:50',
                'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'sometimes|in:0,1,true,false',
                'is_featured' => 'sometimes|in:0,1,true,false',
            ]);

            // Convert string booleans to actual booleans
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            }

            // Handle image uploads
            foreach (['image_1', 'image_2', 'image_3'] as $imageField) {
                if ($request->hasFile($imageField)) {
                    $validated[$imageField] = $this->uploadImage($request->file($imageField));
                }
            }

            $product = Product::create($validated);
            $product->load(['productType', 'category', 'color', 'shape']);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        $product = Product::with(['productType', 'category', 'color', 'shape'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * @OA\Post(
     *     path="/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="_method", type="string", example="PUT"),
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="sku", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="product_type_id", type="integer"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="color_id", type="integer"),
     *                 @OA\Property(property="shape_id", type="integer"),
     *                 @OA\Property(property="price", type="number"),
     *                 @OA\Property(property="weight", type="number"),
     *                 @OA\Property(property="weight_unit", type="string"),
     *                 @OA\Property(property="purity", type="string"),
     *                 @OA\Property(property="image_1", type="string", format="binary"),
     *                 @OA\Property(property="image_2", type="string", format="binary"),
     *                 @OA\Property(property="image_3", type="string", format="binary"),
     *                 @OA\Property(property="is_active", type="boolean"),
     *                 @OA\Property(property="is_featured", type="boolean")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $id,
                'description' => 'nullable|string',
                'product_type_id' => 'sometimes|required|exists:product_types,id',
                'category_id' => 'sometimes|required|exists:categories,id',
                'color_id' => 'nullable|exists:colors,id',
                'shape_id' => 'nullable|exists:shapes,id',
                'price' => 'sometimes|required|numeric|min:0',
                'weight' => 'nullable|numeric|min:0',
                'weight_unit' => 'nullable|string|max:50',
                'purity' => 'nullable|string|max:50',
                'image_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'sometimes|in:0,1,true,false',
                'is_featured' => 'sometimes|in:0,1,true,false',
            ]);

            // Convert string booleans to actual booleans
            if (isset($validated['is_active'])) {
                $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($validated['is_featured'])) {
                $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
            }

            // Handle image uploads
            foreach (['image_1', 'image_2', 'image_3'] as $imageField) {
                if ($request->hasFile($imageField)) {
                    // Delete old image if exists
                    if ($product->$imageField) {
                        Storage::disk('public')->delete($product->$imageField);
                    }
                    $validated[$imageField] = $this->uploadImage($request->file($imageField));
                }
            }

            $product->update($validated);
            $product->load(['productType', 'category', 'color', 'shape']);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);

            // Delete images
            foreach (['image_1', 'image_2', 'image_3'] as $imageField) {
                if ($product->$imageField) {
                    Storage::disk('public')->delete($product->$imageField);
                }
            }

            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image to storage
     */
    private function uploadImage($file)
    {
        $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('products', $filename, 'public');
        return $path;
    }
}
