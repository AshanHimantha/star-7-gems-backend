# 🚀 Quick Reference - Auth API

## 📍 Swagger UI
```
http://localhost:8000/api/documentation
```

## 🔑 Endpoints

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/register` | ❌ | Register new user |
| POST | `/api/login` | ❌ | Login user |
| POST | `/api/logout` | ✅ | Logout user |
| GET | `/api/me` | ✅ | Get current user |
| GET | `/api/user` | ✅ | Get user (alt) |

## 📤 Request Examples

### Register
```json
POST /api/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Login
```json
POST /api/login
{
  "email": "john@example.com",
  "password": "password123"
}
```

### Protected Routes
```
GET /api/me
Headers: Authorization: Bearer {your_token}
```

## 📥 Response Format

### Success (Register/Login)
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2025-12-01T16:57:36.000000Z",
      "updated_at": "2025-12-01T16:57:36.000000Z"
    },
    "access_token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

### Error (422)
```json
{
  "message": "The email has already been taken.",
  "errors": {
    "email": ["The email has already been taken."]
  }
}
```

## 🛠️ Commands

```bash
# Start server
php artisan serve

# Regenerate Swagger docs
php artisan l5-swagger:generate

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
```

## 🧪 Testing

### Swagger UI (Easiest)
1. Go to `http://localhost:8000/api/documentation`
2. Click endpoint → "Try it out" → Fill data → "Execute"

### cURL
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

### PowerShell
```powershell
.\test-auth-api.ps1
```

## 📋 Checklist

- ✅ Laravel Sanctum installed
- ✅ L5-Swagger installed
- ✅ Database migrated
- ✅ User model updated
- ✅ API routes configured
- ✅ AuthController created
- ✅ Swagger annotations added
- ✅ Documentation generated
- ✅ Tested successfully

## 🔗 Documentation Files

- `README_AUTH_SETUP.md` - Complete setup guide
- `SETUP_COMPLETE.md` - Quick start guide
- `API_DOCUMENTATION.md` - Detailed API docs
- `http://localhost:8000/api/documentation` - Interactive docs

---

**Ready to code! 🎉**
