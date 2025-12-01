# Swagger API Documentation Configuration

## Overview
The Swagger/OpenAPI documentation for the Star 7 Gems API is configured to dynamically use the correct server URL based on your environment (local development or deployed production).

## Configuration

### Environment Variable
The API documentation URL is controlled by the `L5_SWAGGER_CONST_HOST` environment variable in your `.env` file.

### Local Development
For local development, use:
```bash
L5_SWAGGER_CONST_HOST=http://localhost:8000/api
```

### Production/Deployed Environment
When deploying to production, update your `.env` file with your deployed domain:
```bash
L5_SWAGGER_CONST_HOST=https://your-domain.com/api
```

**Examples:**
- `L5_SWAGGER_CONST_HOST=https://api.star7gems.com/api`
- `L5_SWAGGER_CONST_HOST=https://star7gems-backend.herokuapp.com/api`
- `L5_SWAGGER_CONST_HOST=https://your-app.railway.app/api`

## Regenerating Documentation

After changing the environment variable, regenerate the Swagger documentation:

```bash
php artisan l5-swagger:generate
```

This will update the API documentation to use the new server URL.

## Accessing the Documentation

Once configured, you can access the Swagger UI at:
- **Local:** `http://localhost:8000/api/documentation`
- **Production:** `https://your-domain.com/api/documentation`

## Important Notes

1. **Always use HTTPS in production** for security
2. **Include the `/api` path** in the URL if your API routes are prefixed with `/api`
3. **Regenerate docs after deployment** if you're using a dynamic deployment URL
4. The documentation will automatically use the configured URL for all "Try it out" requests

## Configuration File Location

The Swagger configuration is defined in:
- **Main annotation:** `app/Http/Controllers/Api/AuthController.php` (lines 21-24)
- **L5-Swagger config:** `config/l5-swagger.php` (line 315)
- **Environment variable:** `.env` file

## Troubleshooting

If the documentation still shows localhost after deployment:
1. Verify the `L5_SWAGGER_CONST_HOST` variable is set in your production `.env` file
2. Clear the config cache: `php artisan config:clear`
3. Regenerate the documentation: `php artisan l5-swagger:generate`
4. Clear the application cache: `php artisan cache:clear`
