<p align="center">
  <img src="https://seolinkplace.com/images/og-home.png" alt="SEOLinkPlace" width="800">
</p>

<h1 align="center">SEOLinkPlace Community Edition</h1>

<p align="center">
  <strong>Open source link placement marketplace — self-hosted, modular, production-ready.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=flat-square&logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Filament-5-f59e0b?style=flat-square" alt="Filament 5">
  <img src="https://img.shields.io/badge/license-MIT-green?style=flat-square" alt="MIT">
</p>

---

## What is this?

SEOLinkPlace Community Edition is the open source core of [seolinkplace.com](https://seolinkplace.com) — a B2B marketplace for SEO link placement.

This repository contains the **foundation** you need to run your own link placement platform:

- Multi-role user system (clients, webmasters, performers)
- Site registry with domain verification
- Admin panel powered by Filament 5
- Bilingual interface (Ukrainian / English)
- Google OAuth
- Support ticket system
- Public blog

## Architecture

Built as a **modular monolith** using [nwidart/laravel-modules](https://nwidart.com/laravel-modules). Each module is self-contained with its own models, controllers, Filament resources, routes, and migrations.

```
Modules/
├── Core/      — Users, roles, authentication, admin panel
├── Auth/      — Login, registration, Google OAuth
├── Sites/     — Site registry and domain verification
├── Blog/      — Public bilingual blog
└── Support/   — Support ticket system
```

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.3 |
| Admin panel | Filament 5 |
| Database | MariaDB 11+ / MySQL 8+ |
| Cache / Sessions | Redis |
| Web server | Nginx + PHP-FPM |

## Requirements

- PHP 8.3+
- MariaDB 11+ or MySQL 8+
- Redis
- Composer 2
- Node.js 20+

## Installation

```bash
# Clone
git clone https://github.com/seolinkplace/community.git
cd community

# Install PHP dependencies
composer install

# Environment setup
cp .env.example .env
php artisan key:generate

# Edit .env — set DB_*, REDIS_*, MAIL_* and APP_NAME, APP_URL

# Run migrations
php artisan migrate

# Build frontend
npm install && npm run build

# Storage permissions
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### First admin user

```bash
php artisan make:filament-user
```

Then open `/admin` in your browser.

## Configuration

All platform settings are managed via `APP_NAME` and `APP_URL` in your `.env` file. The application name is used throughout the UI automatically — no hardcoded branding.

### Google OAuth (optional)

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

### hCaptcha (optional)

```env
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key
```

## Premium Modules

The following modules are available as part of **SEOLinkPlace Enterprise Edition**:

| Module | Description |
|---|---|
| Campaigns | Campaign management, link placement, daily billing |
| Wallet | Client & webmaster wallets, transactions |
| Billing | Cryptomus, NOWPayments, USDT TRC20 payments |
| Articles | Article ordering with approval flow |
| Parser | Go microservice for automated link checking |
| Affiliate | Referral program with commission tracking |
| Tasks | Performer task board with auto-approve |

Contact [seolinkplace/contact](https://seolinkplace.com/contact) for licensing.

## Documentation

- [INSTALL.md](./INSTALL.md) — detailed installation guide
- [SYSTEM_DESIGN.md](./SYSTEM_DESIGN.md) — full architecture documentation

## Live Demo

[seolinkplace.com](https://seolinkplace.com) runs the full Enterprise Edition.

## License

MIT License — see [LICENSE](./LICENSE) for details.

The Community Edition is free to use, modify, and distribute.
Premium modules are proprietary and require a separate license.

