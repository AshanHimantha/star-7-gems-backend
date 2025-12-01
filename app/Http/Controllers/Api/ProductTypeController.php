<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Illuminate\Http\Request;

class ProductTypeController extends Controller
{
    /**
     * Display a listing of product types
     * 
     * @OA\Get(
     *     path="/product-types",
     *     summary="Get all product types",
     *     tags={"Product Types"},
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Gemstone"),
     *                 @OA\Property(property="description", type="string", example="Natural gemstones"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = ProductType::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $productTypes = $query->get();

        return response()->json([
            'success' => true,
            'data' => $productTypes
        ]);
    }

    /**
     * Store a newly created product type
     * 
     * @OA\Post(
     *     path="/product-types",
     *     summary="Create a new product type",
     *     tags={"Product Types"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Gemstone"),
     *             @OA\Property(property="description", type="string", example="Natural gemstones"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product type created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product type created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:product_types',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $productType = ProductType::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product type created successfully',
                'data' => $productType
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product type creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product type
     * 
     * @OA\Get(
     *     path="/product-types/{id}",
     *     summary="Get a specific product type",
     *     tags={"Product Types"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product type not found")
     * )
     */
    public function show($id)
    {
        try {
            $productType = ProductType::with('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $productType
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product type not found'
            ], 404);
        }
    }

    /**
     * Update the specified product type
     * 
     * @OA\Put(
     *     path="/product-types/{id}",
     *     summary="Update a product type",
     *     tags={"Product Types"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Gemstone"),
     *             @OA\Property(property="description", type="string", example="Natural gemstones"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product type updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product type updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product type not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $productType = ProductType::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:product_types,name,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $productType->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Product type updated successfully',
                'data' => $productType
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product type not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product type update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product type',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product type
     * 
     * @OA\Delete(
     *     path="/product-types/{id}",
     *     summary="Delete a product type",
     *     tags={"Product Types"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product type deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product type deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product type not found")
     * )
     */
    public function destroy($id)
    {
        try {
            $productType = ProductType::findOrFail($id);
            $productType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product type deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product type not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product type deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product type',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
