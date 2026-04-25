# Installation Guide

## Requirements

- PHP 8.3+
- MySQL 8.0+ or MariaDB 11+
- Redis
- Composer
- Node.js 20+
- Nginx + PHP-FPM

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/seolinkplace/community.git
cd community

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Configure your .env file
# Set DB_*, REDIS_*, MAIL_* values

# 5. Run migrations
php artisan migrate --seed

# 6. Build frontend assets
npm install && npm run build

# 7. Set permissions
chmod -R 775 storage bootstrap/cache

# 8. Start the application
php artisan serve
```

## Module System

SEOLinkPlace Community Edition uses [nwidart/laravel-modules](https://nwidart.com/laravel-modules).

### Included modules (Community Edition)

| Module | Description |
|--------|-------------|
| Core | Users, roles, authentication, admin panel |
| Auth | Login, registration, Google OAuth |
| Sites | Site registry and verification |
| Blog | Public bilingual blog |
| Support | Support ticket system |

### Premium modules

Additional modules (Campaigns, Wallet, Billing, Articles, Tasks, Parser, Affiliate)
are available as part of SEOLinkPlace Enterprise Edition.

Contact: hello@seolinkplace.com

## Admin Panel

Access the admin panel at `/admin`. Create the first admin user:

```bash
php artisan make:filament-user
```

## Documentation

See [SYSTEM_DESIGN.md](./SYSTEM_DESIGN.md) for full architecture documentation.
