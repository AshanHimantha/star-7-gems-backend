# 💎 Product Management System Documentation

## Overview
The Star 7 Gems backend now includes a complete Product Management System with the following components:
- **Product Types** (e.g., Gemstone, Jewelry)
- **Categories** (e.g., Rings, Necklaces)
- **Colors** (e.g., Ruby Red, Sapphire Blue)
- **Shapes** (e.g., Round, Oval, Princess)
- **Products** (The main inventory items)

## 🔗 API Endpoints

### 1. Product Types
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/product-types` | ❌ | List all product types |
| GET | `/api/product-types/{id}` | ❌ | Get specific product type |
| POST | `/api/product-types` | ✅ | Create product type |
| PUT | `/api/product-types/{id}` | ✅ | Update product type |
| DELETE | `/api/product-types/{id}` | ✅ | Delete product type |

### 2. Categories
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/categories` | ❌ | List all categories |
| GET | `/api/categories/{id}` | ❌ | Get specific category |
| POST | `/api/categories` | ✅ | Create category |
| PUT | `/api/categories/{id}` | ✅ | Update category |
| DELETE | `/api/categories/{id}` | ✅ | Delete category |

### 3. Colors
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/colors` | ❌ | List all colors |
| GET | `/api/colors/{id}` | ❌ | Get specific color |
| POST | `/api/colors` | ✅ | Create color |
| PUT | `/api/colors/{id}` | ✅ | Update color |
| DELETE | `/api/colors/{id}` | ✅ | Delete color |

### 4. Shapes
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/shapes` | ❌ | List all shapes |
| GET | `/api/shapes/{id}` | ❌ | Get specific shape |
| POST | `/api/shapes` | ✅ | Create shape |
| PUT | `/api/shapes/{id}` | ✅ | Update shape |
| DELETE | `/api/shapes/{id}` | ✅ | Delete shape |

### 5. Products
| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/products` | ❌ | List products (with filters) |
| GET | `/api/products/{id}` | ❌ | Get specific product |
| POST | `/api/products` | ✅ | Create product (Multipart) |
| POST | `/api/products/{id}` | ✅ | Update product (Multipart) |
| DELETE | `/api/products/{id}` | ✅ | Delete product |

> **Note:** The update endpoint uses `POST` method directly (not PUT) because PHP/Laravel has limitations handling `PUT` requests with `multipart/form-data` (required for image uploads). No `_method` field is needed.

## 🔍 Filtering & Search
The `/api/products` endpoint supports extensive filtering:
- `?search=ring` (Search by name, SKU, description)
- `?category_id=1`
- `?product_type_id=1`
- `?color_id=1`
- `?shape_id=1`
- `?min_price=100&max_price=500`
- `?is_featured=1`
- `?is_active=1`

## 📦 Data Models

### Product Schema
```json
{
  "id": 1,
  "name": "Blue Sapphire Ring",
  "sku": "BSR-001",
  "description": "Elegant 18K gold ring...",
  "price": 299.99,
  "weight": 2.5,
  "weight_unit": "grams",
  "purity": "18K",
  "images": [
    "products/image1.jpg",
    "products/image2.jpg"
  ],
  "product_type": { ... },
  "category": { ... },
  "color": { ... },
  "shape": { ... }
}
```

## 🧪 Testing with cURL

### Create a Category
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"name":"Rings", "description":"All rings"}'
```

### Create a Product
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Authorization: Bearer {token}" \
  -F "name=Ruby Ring" \
  -F "sku=RR-001" \
  -F "price=500" \
  -F "category_id=1" \
  -F "product_type_id=1" \
  -F "image_1=@/path/to/image.jpg"
```

## 📚 Swagger Documentation
Full interactive documentation is available at:
`http://localhost:8000/api/documentation`
