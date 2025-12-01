<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shape;
use Illuminate\Http\Request;

class ShapeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/shapes",
     *     summary="Get all shapes",
     *     tags={"Shapes"},
     *     @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = Shape::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $shapes = $query->get();

        return response()->json([
            'success' => true,
            'data' => $shapes
        ]);
    }

    /**
     * @OA\Post(
     *     path="/shapes",
     *     summary="Create a new shape",
     *     tags={"Shapes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Round"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Shape created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:shapes',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $shape = Shape::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Shape created successfully',
                'data' => $shape
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shape creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create shape',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/shapes/{id}",
     *     summary="Get a specific shape",
     *     tags={"Shapes"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        try {
            $shape = Shape::with('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $shape
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shape not found'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/shapes/{id}",
     *     summary="Update a shape",
     *     tags={"Shapes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Shape updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $shape = Shape::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:shapes,name,' . $id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $shape->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Shape updated successfully',
                'data' => $shape
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shape not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shape update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shape',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/shapes/{id}",
     *     summary="Delete a shape",
     *     tags={"Shapes"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Shape deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $shape = Shape::findOrFail($id);
            $shape->delete();

            return response()->json([
                'success' => true,
                'message' => 'Shape deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shape not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Shape deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete shape',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
