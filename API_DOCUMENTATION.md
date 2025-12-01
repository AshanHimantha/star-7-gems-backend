# Authentication API Documentation

## Base URL
```
http://localhost:8000/api
```

## Endpoints

### 1. Register
Create a new user account.

**Endpoint:** `POST /register`
 
**Headers:**
```
Authorization: Bearer {access_token}
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
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

---

### 2. Login
Authenticate a user and receive an access token.

**Endpoint:** `POST /login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-12-01T16:57:36.000000Z",
            "updated_at": "2025-12-01T16:57:36.000000Z"
        },
        "access_token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

---

### 3. Get Current User (Me)
Get the authenticated user's information.

**Endpoint:** `GET /me`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2025-12-01T16:57:36.000000Z",
            "updated_at": "2025-12-01T16:57:36.000000Z"
        }
    }
}
```

---

### 4. Logout
Revoke the current access token.

**Endpoint:** `POST /logout`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

### 5. Get User (Alternative)
Alternative endpoint to get the authenticated user.

**Endpoint:** `GET /user`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200):**
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2025-12-01T16:57:36.000000Z",
    "updated_at": "2025-12-01T16:57:36.000000Z"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
    "message": "The email has already been taken. (and 1 more error)",
    "errors": {
        "email": [
            "The email has already been taken."
        ],
        "password": [
            "The password field confirmation does not match."
        ]
    }
}
```

### Authentication Error (401)
```json
{
    "message": "Unauthenticated."
}
```

### Invalid Credentials (422)
```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": [
            "The provided credentials are incorrect."
        ]
    }
}
```

---

## Testing with cURL

### Register
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### Get Current User
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  -H "Accept: application/json"
```

---

## Notes

- All protected routes require the `Authorization: Bearer {token}` header
- Tokens are automatically revoked when logging in (previous tokens are deleted)
- Passwords are hashed using Laravel's Hash facade
- Email must be unique in the system
- Password must be at least 8 characters long
- Password confirmation is required during registration
