# Security Policy

## Supported Versions

We actively maintain security for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| Latest  | :white_check_mark: |

## Security Features Implemented

This application implements security best practices based on OWASP Top 10 (2021) and Laravel security guidelines.

### A01:2021 - Broken Access Control

✅ **Implemented:**
- FilamentUser contract implemented for admin panel access control
- Authorization policies created for all resources (Agenda, Post, Apbdes, Official, HeroSlide, Statistic, MenuItem, QuickLink)
- Rate limiting on public routes (60-120 requests per minute)
- Input validation on all route parameters

### A02:2021 - Cryptographic Failures

✅ **Implemented:**
- Environment variables stored in `.env` file (excluded from version control)
- `.env.example` file provided with all required variables
- Password hashing using Laravel's default bcrypt
- Database files excluded from git repository

### A03:2021 - Injection

✅ **Implemented:**
- Mass assignment protection using explicit `$fillable` arrays (no `$guarded = []`)
- URL redirection validation and whitelisting (SSRF protection)
- Route parameter validation (slug, id, label format checks)
- Laravel ORM uses parameterized queries (SQL injection prevention)

### A04:2021 - Insecure Design

✅ **Implemented:**
- Request validation classes for user inputs
- Rate limiting middleware on public routes
- CSRF protection enabled via Laravel middleware

### A05:2021 - Security Misconfiguration

✅ **Implemented:**
- Content Security Policy (CSP) headers
- X-Content-Type-Options: nosniff
- X-Frame-Options: SAMEORIGIN
- Referrer-Policy: strict-origin-when-cross-origin
- Permissions-Policy headers
- Strict-Transport-Security (HSTS) for HTTPS connections
- X-XSS-Protection header
- Production environment checks (APP_DEBUG=false required)

### A06:2021 - Vulnerable Components

⚠️ **Recommended:**
- Run `composer audit` regularly
- Keep dependencies updated
- Monitor security advisories

### A07:2021 - Authentication Failures

✅ **Implemented:**
- FilamentUser contract for production access control
- Password hashing via Laravel
- Session security configured (httponly, secure cookies)

### A08:2021 - Software and Data Integrity Failures

✅ **Implemented:**
- File upload validation (MIME types: image/jpeg, image/png, image/webp, image/gif)
- File size limits (2-5MB depending on upload type)
- Image conversion to WebP format

### A09:2021 - Security Logging and Monitoring

✅ **Implemented:**
- Audit logging for admin actions (create, update, delete) via AuditLogObserver
- Actions logged to Laravel log file with user info, IP address, and changes
- **Log injection prevention**: All user-provided data sanitized before logging (removes newlines, control characters)
- Failed login attempts tracked by Laravel authentication system
- Exception handler configured to hide sensitive information in production
⚠️ **Recommended:**
- Set up external application monitoring (e.g., Sentry, Bugsnag)
- Configure log rotation and archival
- Set up alerts for suspicious activities

### A10:2021 - Server-Side Request Forgery (SSRF)

✅ **Implemented:**
- URL validation and whitelisting in QuickLinkController
- Blocked redirects to private/local IP addresses
- Validated redirect URLs before redirecting

## Additional Security Measures

### Input Validation

All route parameters are validated:
- Slug format: alphanumeric, hyphens, underscores only
- ID format: numeric only
- Label format: alphanumeric, hyphens, underscores, max 100 characters

### Output Encoding

- HTML output sanitized using Filament's `sanitizeHtml()` helper
- User-generated content escaped in Blade templates (`{{ }}`)

### File Upload Security

- Maximum file sizes enforced (2-5MB)
- MIME type validation
- Only image files accepted
- Files stored in public disk with proper permissions

## Reporting a Vulnerability

If you discover a security vulnerability, please email security@example.com. Do not create a public GitHub issue.

## Security Checklist for Deployment

Before deploying to production:

- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Generate new `APP_KEY` if not already set
- [ ] Configure database credentials securely
- [ ] Enable HTTPS/SSL certificates
- [ ] Configure rate limiting appropriately
- [ ] Set up proper file permissions (storage/, bootstrap/cache/)
- [ ] Configure session driver and security settings
- [ ] Review and configure security headers
- [ ] Run `composer audit` and fix vulnerabilities
- [ ] Set up backup procedures
- [ ] Configure logging and monitoring
- [ ] Test all admin panel access controls
- [ ] Review file upload configurations

## Security Headers Configuration

Security headers are automatically applied via `SecurityHeaders` middleware. See `app/Http/Middleware/SecurityHeaders.php` for details.

## Environment Variables

Required environment variables are documented in `config/app.php` and should be set in `.env` file. Never commit `.env` files to version control.

### Security-Related Environment Variables

- `APP_ADMIN_EMAIL_DOMAIN`: Restrict admin panel access by email domain (e.g., `example.com`). Leave empty to allow all verified emails.
- `ALLOWED_REDIRECT_DOMAINS`: Comma-separated list of allowed domains for external redirects (SSRF protection). Example: `google.com,youtube.com,example.com`. Leave empty to allow all external domains (not recommended for production).
- `APP_DEBUG`: Must be `false` in production
- `APP_ENV`: Must be `production` in production

## Rate Limiting

Public routes are rate-limited:
- Homepage: 120 requests/minute
- Other public routes: 60 requests/minute
- Admin routes: Protected by authentication

## Dependencies

Regularly audit dependencies:
```bash
composer audit
```

Update dependencies regularly:
```bash
composer update
```

## References

- [OWASP Top 10 (2021)](https://owasp.org/Top10/)
- [Laravel Security Documentation](https://laravel.com/docs/security)
- [Filament Security Guidelines](https://filamentphp.com/docs/security)

