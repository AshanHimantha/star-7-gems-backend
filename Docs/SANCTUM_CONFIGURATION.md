# 🔐 Sanctum Stateful Domains Configuration

## What This Does
Sanctum's stateful domains configuration tells Laravel which frontend domains should receive stateful authentication cookies. This is essential for SPA (Single Page Application) authentication.

## Required Configuration

### Add to your `.env` file:

```env
# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000,127.0.0.1:3000,127.0.0.1:5173
```

### Explanation of Domains:
- **`localhost`** - Base localhost domain
- **`localhost:3000`** - Common React development port
- **`localhost:5173`** - Vite default port (Vue, React with Vite)
- **`127.0.0.1`** - IP equivalent of localhost
- **`127.0.0.1:8000`** - Laravel development server
- **`127.0.0.1:3000`** - React on IP address
- **`127.0.0.1:5173`** - Vite on IP address

## How to Update

1. Open your `.env` file
2. Add the line above at the end of the file (or in the Sanctum section if you have one)
3. Save the file
4. **Restart your Laravel server** for changes to take effect:
   ```bash
   # Stop the current server (Ctrl+C)
   # Then restart:
   php artisan serve
   ```

## For Production

When deploying to production, update this to include your production frontend domain:

```env
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com,localhost,127.0.0.1
```

## Additional CORS Configuration

If you're building a separate frontend application, you may also need to configure CORS in `config/cors.php`:

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],

'allowed_origins' => [
    'http://localhost:3000',
    'http://localhost:5173',
    'http://127.0.0.1:3000',
    'http://127.0.0.1:5173',
],

'supports_credentials' => true,
```

## Testing

After configuration, your frontend can authenticate using:

1. **Get CSRF Cookie** (first):
   ```javascript
   await axios.get('http://localhost:8000/sanctum/csrf-cookie');
   ```

2. **Login**:
   ```javascript
   await axios.post('http://localhost:8000/api/login', {
       email: 'user@example.com',
       password: 'password'
   }, {
       withCredentials: true
   });
   ```

3. **Make Authenticated Requests**:
   ```javascript
   await axios.get('http://localhost:8000/api/me', {
       withCredentials: true
   });
   ```

## Notes
- The `withCredentials: true` option is crucial for cookie-based authentication
- Ensure your frontend axios instance is configured with `withCredentials: true`
- For token-based authentication (mobile apps), use the `Authorization: Bearer {token}` header instead
