# Product API Boolean Field Fix

## Issue
When sending product creation/update requests via `multipart/form-data` (required for image uploads), boolean fields (`is_active`, `is_featured`) were being sent as strings (`"true"`, `"false"`) instead of actual boolean values, causing validation errors.

## Error Message
```json
{
  "success": false,
  "message": "Failed to create product",
  "error": "The is active field must be true or false. (and 1 more error)"
}
```

## Solution
Updated the `ProductController` validation and data processing to handle string boolean values from multipart/form-data requests.

### Changes Made

1. **Updated Validation Rules** (lines 136-137, 239-240)
   - Changed from: `'is_active' => 'boolean'`
   - Changed to: `'is_active' => 'sometimes|in:0,1,true,false'`
   - This accepts: `0`, `1`, `"0"`, `"1"`, `"true"`, `"false"`

2. **Added Boolean Conversion Logic** (after validation)
   ```php
   // Convert string booleans to actual booleans
   if (isset($validated['is_active'])) {
       $validated['is_active'] = filter_var($validated['is_active'], FILTER_VALIDATE_BOOLEAN);
   }
   if (isset($validated['is_featured'])) {
       $validated['is_featured'] = filter_var($validated['is_featured'], FILTER_VALIDATE_BOOLEAN);
   }
   ```

## Correct Usage

### ✅ Valid Boolean Values
You can now use any of these formats:
- `is_active=true` or `is_active=false`
- `is_active=1` or `is_active=0`
- `is_active="true"` or `is_active="false"`
- `is_active="1"` or `is_active="0"`

### Example cURL Request
```bash
curl -X 'POST' \
  'https://7stars.1000dtechnology.com/api/products' \
  -H 'accept: */*' \
  -H 'Authorization: Bearer YOUR_TOKEN' \
  -H 'Content-Type: multipart/form-data' \
  -F 'name=Blue Sapphire Ring' \
  -F 'sku=BSR-001' \
  -F 'description=Elegant 18K gold ring' \
  -F 'product_type_id=1' \
  -F 'category_id=2' \
  -F 'color_id=1' \
  -F 'shape_id=1' \
  -F 'price=299.99' \
  -F 'weight=2.5' \
  -F 'weight_unit=carats' \
  -F 'purity=18K' \
  -F 'is_active=true' \
  -F 'is_featured=false' \
  -F 'image_1=@image1.jpg;type=image/jpeg' \
  -F 'image_2=@image2.jpg;type=image/jpeg' \
  -F 'image_3=@image3.jpg;type=image/jpeg'
```

### ⚠️ Important Notes

1. **Stock Field Removed**: Do NOT include `stock` in your requests. This field has been removed from the product system.

2. **Boolean Fields Are Optional**: Both `is_active` and `is_featured` are optional. If not provided:
   - `is_active` defaults to `true`
   - `is_featured` defaults to `false`

3. **Multipart/Form-Data Required**: When uploading images, you MUST use `Content-Type: multipart/form-data`

## Testing

After this fix, the same request that previously failed will now work correctly. The boolean values will be properly converted and stored in the database.

## Files Modified
- `app/Http/Controllers/Api/ProductController.php`
  - Updated validation rules in `store()` method
  - Updated validation rules in `update()` method
  - Added boolean conversion logic in both methods
