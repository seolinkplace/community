<p align="center">
  <img src="https://seolinkplace.com/images/og-home.png" alt="SEOLinkPlace" width="800">
</p>

<h1 align="center">SEOLinkPlace Community Edition</h1>

<p align="center">
  <strong>Open source link placement marketplace — self-hosted, modular, production-ready.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=flat-square&logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Filament-5-f59e0b?style=flat-square" alt="Filament 5">
  <img src="https://img.shields.io/badge/license-MIT-green?style=flat-square" alt="MIT">
</p>

<p align="center">
  <a href="./README.uk.md">🇺🇦 Читати українською</a>
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
| Backend | Laravel 13, PHP 8.4 |
| Admin panel | Filament 5 |
| Database | MariaDB 11+ / MySQL 8+ |
| Cache / Sessions | Redis |
| Web server | Nginx + PHP-FPM |

## Requirements

- PHP 8.4+
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

---

## Commercial Modules

The Community Edition is the open core. The following modules extend it into a full production marketplace.

| Module | Description | Price |
|---|---|---|
| **AiContent** | AI-powered content generation for webmasters. Prompt templates with dynamic variables, Gemini 2.5 Flash integration, scheduling, revision history with word-level diff. | **$149** |
| **Billing** | Payment processing with Cryptomus, NOWPayments, and USDT TRC20 out of the box. Clean payment provider interface — any provider with an API (Stripe, PayPal, LiqPay, etc.) can be integrated. | **$129** |
| **Wallet** | Client, webmaster, and performer wallets. Transaction history, balance management, withdrawal system. | **$129** |
| **Campaigns** | Client-side campaign management. Link placement orders, daily billing against wallet balance, campaign analytics. | **$99** |
| **Articles** | Article ordering workflow between clients and webmasters. Client places an order, webmaster writes and submits, client approves. | **$89** |
| **Parser** | Go microservice for automated link verification. Crawls target pages on schedule, detects link presence, suspends placements on repeated failures. | **$89** |
| **Affiliate** | Referral program with commission tracking, referral links, and automated payouts to affiliate wallets. | **$69** |
| **Tasks** | Performer task board. DB-driven task types, auto-approve logic, reward payouts on completion. | **$49** |

### Full Bundle

> **All 8 modules — $599** *(save $203 vs purchasing individually)*

---

## Why these prices?

Building these modules from scratch with a hired developer costs approximately **$10,000–14,000**:

| Module | Estimated hours | Cost @ $40/hr |
|---|---|---|
| AiContent | 60–80 hrs | $2,400–3,200 |
| Billing + Wallet | 50–70 hrs | $2,000–2,800 |
| Campaigns | 40–60 hrs | $1,600–2,400 |
| Articles | 30–40 hrs | $1,200–1,600 |
| Parser (Go) | 30–40 hrs | $1,200–1,600 |
| Affiliate | 20–30 hrs | $800–1,200 |
| Tasks | 15–20 hrs | $600–800 |
| **Total** | **245–340 hrs** | **$9,800–13,600** |

The full bundle at **$599** represents a ~95% discount against custom development cost.

For comparison: a monthly subscription to a comparable hosted marketplace platform (Collaborator, Getfound, Miralinks) costs more than the full bundle — and you don't own the code.

---

## Installation & Setup Service

Prefer to have everything set up for you? We offer a professional deployment service:

| Service | Includes | Price |
|---|---|---|
| **Community Edition setup** | VPS configuration, Nginx + PHP-FPM + Redis, SSL, deployment, `.env` configuration, first admin user, smoke testing | **$799** |
| **Enterprise setup** | Everything above + all commercial modules, queue worker (Supervisor), Go parser deployment, payment provider configuration | **$1,499** |

Contact via [seolinkplace.com/contact](https://seolinkplace.com/contact).

---

## Documentation

- [INSTALL.md](./INSTALL.md) — detailed installation guide
- [SYSTEM_DESIGN.md](./SYSTEM_DESIGN.md) — full architecture documentation

## Live Demo

[seolinkplace.com](https://seolinkplace.com) runs the full Enterprise Edition.

## License

MIT License — see [LICENSE](./LICENSE) for details.

The Community Edition is free to use, modify, and distribute.
Commercial modules are proprietary and require a separate license.
