# 💎 Star 7 Gems Backend

A robust Laravel-based REST API for managing a jewelry and gemstone e-commerce platform. Built with Laravel 12, featuring comprehensive authentication, product management, and full API documentation.

## 🚀 Features

### 🔐 Authentication & Security
- **Laravel Sanctum** - Token-based API authentication
- **Protected Registration** - Only authenticated users can create new accounts
- **Comprehensive Error Handling** - Graceful error responses with logging
- **CORS Support** - Configured for SPA integration

### 📦 Product Management System
- **Product Types** - Gemstones, Jewelry, etc.
- **Categories** - Rings, Necklaces, Earrings, Bracelets, Pendants
- **Colors** - With hex code support
- **Shapes** - Round, Oval, Cushion, etc.
- **Products** - Full CRUD with image uploads (up to 3 images per product)
- **Advanced Filtering** - Search, price range, category, type, color, shape filters
- **Pagination** - Efficient data loading

### 📚 API Documentation
- **Swagger/OpenAPI** - Interactive API documentation at `/api/documentation`
- **L5-Swagger** - Auto-generated from controller annotations
- **Request/Response Examples** - Complete with validation rules

### 🧪 Testing
- **Feature Tests** - Authentication, Product System, Seeders
- **17 Tests** - 62 assertions covering critical functionality
- **RefreshDatabase** - Clean test environment

### 🌱 Database Seeding
- **Static Data Seeders** - Product types, categories, colors, shapes
- **Dummy Data** - 50 sample products with factories
- **Relationships** - Fully seeded with proper associations

## 📋 Requirements

- PHP 8.2+
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd star-7-gems-backend
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=star_7_gems
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Configure Sanctum (for SPA)
Add to `.env`:
```env
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000
```

### 6. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Start Development Server
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 📖 API Documentation

### Access Swagger UI
```
http://localhost:8000/api/documentation
```

### Quick Start

#### 1. Login (Get Token)
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

#### 2. Use Token for Authenticated Requests
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## 🗂️ Project Structure

```
star-7-gems-backend/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   ├── ProductTypeController.php
│   │   ├── ColorController.php
│   │   └── ShapeController.php
│   └── Models/
│       ├── Product.php
│       ├── Category.php
│       ├── ProductType.php
│       ├── Color.php
│       └── Shape.php
├── database/
│   ├── factories/
│   │   └── ProductFactory.php
│   ├── migrations/
│   └── seeders/
│       ├── ProductSeeder.php
│       └── ProductDummySeeder.php
├── routes/
│   └── api.php
├── tests/
│   └── Feature/
│       ├── AuthTest.php
│       ├── ProductSystemTest.php
│       └── SeederTest.php
└── storage/
    └── app/public/products/  # Product images
```

## 🔑 API Endpoints

### Authentication
- `POST /api/login` - Login (public)
- `POST /api/register` - Register new user (requires auth)
- `POST /api/logout` - Logout (requires auth)
- `GET /api/me` - Get current user (requires auth)

### Products
- `GET /api/products` - List all products (public)
- `GET /api/products/{id}` - Get product details (public)
- `POST /api/products` - Create product (requires auth)
- `POST /api/products/{id}` - Update product (requires auth)
- `DELETE /api/products/{id}` - Delete product (requires auth)

### Categories, Types, Colors, Shapes
Similar CRUD endpoints for each resource type.

See full documentation at `/api/documentation`

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
php artisan test tests/Feature/AuthTest.php
php artisan test tests/Feature/ProductSystemTest.php
php artisan test tests/Feature/SeederTest.php
```

### Test Coverage
- ✅ Authentication (8 tests)
- ✅ Product System (6 tests)
- ✅ Database Seeders (1 test)
- ✅ Total: 17 tests, 62 assertions

## 📚 Documentation Files

- `README_AUTH_SETUP.md` - Sanctum & Swagger setup guide
- `API_DOCUMENTATION.md` - Detailed API reference
- `SANCTUM_CONFIGURATION.md` - SPA authentication setup
- `SWAGGER_CONFIGURATION.md` - Swagger deployment configuration
- `ERROR_HANDLING_SUMMARY.md` - Error handling implementation
- `TEST_RESULTS.md` - Test suite documentation
- `PRODUCT_SYSTEM_DOCS.md` - Product system overview

## 🛡️ Error Handling

All controllers implement comprehensive error handling:
- **404 Not Found** - Resource doesn't exist
- **422 Validation Error** - Invalid input data
- **500 Internal Server Error** - Unexpected errors (logged)
- **401 Unauthorized** - Authentication required

All errors are logged to `storage/logs/laravel.log`

## 🔄 Database Seeding

### Seed Static Data Only
```bash
php artisan db:seed --class=ProductSeeder
```

### Seed Dummy Products
```bash
php artisan db:seed --class=ProductDummySeeder
```

### Seed Everything
```bash
php artisan db:seed
```

## 🚢 Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Update `SANCTUM_STATEFUL_DOMAINS` with production domains
4. **Update `L5_SWAGGER_CONST_HOST`** with production API URL (e.g., `https://your-domain.com/api`)
5. Configure proper database credentials
6. Run migrations: `php artisan migrate --force`
7. Regenerate Swagger docs: `php artisan l5-swagger:generate`
8. Optimize: `php artisan optimize`
9. Cache config: `php artisan config:cache`
10. Cache routes: `php artisan route:cache`

> 📘 **Note:** See `Docs/SWAGGER_CONFIGURATION.md` for detailed Swagger setup instructions.

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Support

For support, email support@star7gems.com or open an issue in the repository.

---

**Built with ❤️ using Laravel 12**
