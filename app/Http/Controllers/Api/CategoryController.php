<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/categories",
     *     summary="Get all categories",
     *     tags={"Categories"},
     *     @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     summary="Create a new category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Rings"),
     *             @OA\Property(property="slug", type="string", example="rings"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Category created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
                'slug' => 'nullable|string|max:255|unique:categories',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Category creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/categories/{id}",
     *     summary="Get a specific category",
     *     tags={"Categories"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show($id)
    {
        try {
            $category = Category::with('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/categories/{id}",
     *     summary="Update a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Category updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $category = Category::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:categories,name,' . $id,
                'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Category update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     summary="Delete a category",
     *     tags={"Categories"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Category deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Category deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
