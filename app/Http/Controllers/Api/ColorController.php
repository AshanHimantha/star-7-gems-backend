<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/colors",
     *     summary="Get all colors",
     *     tags={"Colors"},
     *     @OA\Parameter(name="is_active", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        $query = Color::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $colors = $query->get();

        return response()->json([
            'success' => true,
            'data' => $colors
        ]);
    }

    /**
     * @OA\Post(
     *     path="/colors",
     *     summary="Create a new color",
     *     tags={"Colors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Ruby Red"),
     *             @OA\Property(property="hex_code", type="string", example="#E0115F"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Color created successfully")
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:colors',
                'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'is_active' => 'boolean',
            ]);

            $color = Color::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Color created successfully',
                'data' => $color
            ], 201);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Color creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/colors/{id}",
     *     summary="Get a specific color",
     *     tags={"Colors"},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function show($id)
    {
        try {
            $color = Color::with('products')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $color
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/colors/{id}",
     *     summary="Update a color",
     *     tags={"Colors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="hex_code", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Color updated successfully")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $color = Color::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:colors,name,' . $id,
                'hex_code' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
                'is_active' => 'boolean',
            ]);

            $color->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Color updated successfully',
                'data' => $color
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Color update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update color',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/colors/{id}",
     *     summary="Delete a color",
     *     tags={"Colors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Color deleted successfully")
     * )
     */
    public function destroy($id)
    {
        try {
            $color = Color::findOrFail($id);
            $color->delete();

            return response()->json([
                'success' => true,
                'message' => 'Color deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Color not found'
            ], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Color deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete color',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
