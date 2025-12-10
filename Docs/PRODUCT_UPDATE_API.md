# Product Update API - Complete Guide

## Overview
The product update endpoint uses **POST** method (not PUT) to support `multipart/form-data` for image uploads.

## Endpoint
```
POST /api/products/{id}
```

## Authentication
Required: Bearer token in Authorization header

## Important Notes

### ✅ Use POST, Not PUT
- **Correct:** `POST /api/products/1`
- **Wrong:** `PUT /api/products/1`

### ❌ No _method Field Needed
Unlike some Laravel APIs that use method spoofing, this endpoint uses POST directly.
- **Don't include:** `_method=PUT`
- The route is configured as POST in `routes/api.php`

### 📦 Content-Type
Must be `multipart/form-data` when uploading images

## Request Parameters

All fields are **optional** - only send the fields you want to update.

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | No | Product name |
| `sku` | string | No | Stock Keeping Unit (must be unique) |
| `description` | string | No | Product description |
| `product_type_id` | integer | No | Foreign key to product_types table |
| `category_id` | integer | No | Foreign key to categories table |
| `color_id` | integer | No | Foreign key to colors table (nullable) |
| `shape_id` | integer | No | Foreign key to shapes table (nullable) |
| `price` | decimal | No | Product price |
| `weight` | decimal | No | Product weight |
| `weight_unit` | string | No | Unit of weight (e.g., "grams", "carats") |
| `purity` | string | No | Purity specification (e.g., "18K", "24K") |
| `image_1` | file | No | First product image (jpeg, png, jpg, gif, max 2MB) |
| `image_2` | file | No | Second product image |
| `image_3` | file | No | Third product image |
| `is_active` | boolean | No | Product active status (accepts: `true`, `false`, `1`, `0`) |
| `is_featured` | boolean | No | Featured status (accepts: `true`, `false`, `1`, `0`) |

## Example Requests

### Example 1: Update Product Name and Price Only
```bash
curl -X 'POST' \
  'https://7stars.1000dtechnology.com/api/products/1' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: multipart/form-data' \
  -F 'name=Updated Sapphire Ring' \
  -F 'price=349.99'
```

### Example 2: Update Product with New Images
```bash
curl -X 'POST' \
  'https://7stars.1000dtechnology.com/api/products/1' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: multipart/form-data' \
  -F 'name=Blue Sapphire Ring - Premium' \
  -F 'description=Elegant 18K gold ring with natural blue sapphire' \
  -F 'price=299.99' \
  -F 'weight=2.5' \
  -F 'weight_unit=carats' \
  -F 'purity=18K' \
  -F 'is_active=true' \
  -F 'is_featured=true' \
  -F 'image_1=@new_image1.jpg;type=image/jpeg' \
  -F 'image_2=@new_image2.jpg;type=image/jpeg'
```

### Example 3: Update Only Status Fields
```bash
curl -X 'POST' \
  'https://7stars.1000dtechnology.com/api/products/1' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: multipart/form-data' \
  -F 'is_active=false' \
  -F 'is_featured=false'
```

### Example 4: Complete Update with All Fields
```bash
curl -X 'POST' \
  'https://7stars.1000dtechnology.com/api/products/1' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: multipart/form-data' \
  -F 'name=Blue Sapphire Ring' \
  -F 'sku=BSR-001-UPDATED' \
  -F 'description=Premium 18K gold ring with natural blue sapphire' \
  -F 'product_type_id=1' \
  -F 'category_id=2' \
  -F 'color_id=1' \
  -F 'shape_id=1' \
  -F 'price=299.99' \
  -F 'weight=2.5' \
  -F 'weight_unit=carats' \
  -F 'purity=18K' \
  -F 'is_active=1' \
  -F 'is_featured=0' \
  -F 'image_1=@image1.jpg;type=image/jpeg' \
  -F 'image_2=@image2.jpg;type=image/jpeg' \
  -F 'image_3=@image3.jpg;type=image/jpeg'
```

## Response Format

### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Product updated successfully",
  "data": {
    "id": 1,
    "name": "Blue Sapphire Ring",
    "sku": "BSR-001",
    "description": "Elegant 18K gold ring...",
    "price": "299.99",
    "weight": "2.50",
    "weight_unit": "carats",
    "purity": "18K",
    "image_1": "products/abc123.jpg",
    "image_2": "products/def456.jpg",
    "image_3": "products/ghi789.jpg",
    "is_active": true,
    "is_featured": false,
    "product_type_id": 1,
    "category_id": 2,
    "color_id": 1,
    "shape_id": 1,
    "created_at": "2025-12-10T12:00:00.000000Z",
    "updated_at": "2025-12-10T12:30:00.000000Z",
    "product_type": { ... },
    "category": { ... },
    "color": { ... },
    "shape": { ... }
  }
}
```

### Error Response - Product Not Found (404)
```json
{
  "success": false,
  "message": "Product not found"
}
```

### Error Response - Validation Failed (422)
```json
{
  "message": "The sku has already been taken.",
  "errors": {
    "sku": [
      "The sku has already been taken."
    ]
  }
}
```

### Error Response - Server Error (500)
```json
{
  "success": false,
  "message": "Failed to update product",
  "error": "Error details..."
}
```

## Image Handling

### Automatic Old Image Deletion
When you upload a new image to replace an existing one:
1. The old image file is automatically deleted from storage
2. The new image is uploaded and stored
3. The database is updated with the new image path

### Keeping Existing Images
If you don't include an image field in your update request, the existing image is preserved.

### Removing Images
To remove an image without replacing it, you would need to send an empty value or null (implementation may vary).

## Boolean Field Handling

The API accepts multiple formats for boolean fields:
- String: `"true"`, `"false"`
- Integer: `1`, `0`
- String integer: `"1"`, `"0"`
- Boolean: `true`, `false`

All formats are automatically converted to proper boolean values before storage.

## Validation Rules

- **name**: Must be a string, max 255 characters
- **sku**: Must be unique across all products (excluding current product)
- **price**: Must be numeric, minimum 0
- **weight**: Must be numeric, minimum 0
- **product_type_id**: Must exist in product_types table
- **category_id**: Must exist in categories table
- **color_id**: Must exist in colors table (if provided)
- **shape_id**: Must exist in shapes table (if provided)
- **Images**: Must be jpeg, png, jpg, or gif format, max 2MB each

## Common Errors and Solutions

### Error: "The sku has already been taken"
**Solution:** Choose a different SKU or check if another product is using this SKU.

### Error: "The is active field must be true or false"
**Solution:** This error should not occur after the fix. If it does, ensure you're using the updated controller code.

### Error: "Product not found"
**Solution:** Verify the product ID exists in the database.

### Error: "Unauthenticated"
**Solution:** Include a valid Bearer token in the Authorization header.

## Testing in Swagger UI

1. Navigate to: `https://7stars.1000dtechnology.com/api/documentation`
2. Click "Authorize" and enter your Bearer token
3. Find the `POST /products/{id}` endpoint
4. Click "Try it out"
5. Enter the product ID
6. Fill in the fields you want to update
7. Upload images if needed
8. Click "Execute"

## Notes

- Only send fields you want to update - all fields are optional
- The endpoint returns the complete updated product with all relationships loaded
- Images are stored in the `storage/app/public/products` directory
- Old images are automatically cleaned up when replaced
