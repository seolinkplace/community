# SEOLinkPlace — System Design

## Overview

SEOLinkPlace is a B2B SaaS marketplace for SEO link placement that connects **Clients** (buyers of backlinks) with **Webmasters** (site owners who place links). A unified user account can hold multiple roles simultaneously.

- **Stack:** Laravel 13, PHP 8.3, MariaDB 11.4, Redis, Nginx, Supervisor
- **Admin panel:** Filament 5 (latest)
- **Architecture:** Modular monolith via `nwidart/laravel-modules` (13 modules)
- **Payments:** USDT TRC20 (direct), Cryptomus, NOWPayments
- **Parser:** Go microservice at `parser/` managed by Supervisor
- **i18n:** Full bilingual support — Ukrainian (`lang/uk/`) and English (`lang/en/`)
- **Timezone:** `Europe/Kyiv`
- **Database:** `seohands_app` on MariaDB 11.4.7 (80 tables), charset `utf8mb4_unicode_ci`

---

## Dependencies

### Production

| Package | Version | Purpose |
|---|---|---|
| `php` | ^8.3 | Runtime |
| `laravel/framework` | ^13.0 | Core framework (Laravel 13.1.1) |
| `filament/filament` | ^5.4 | Admin panel |
| `laravel/socialite` | ^5.26 | Google OAuth |
| `nwidart/laravel-modules` | ^13.0 | Modular architecture |
| `laravel/tinker` | ^3.0 | REPL (note: no auth context) |

### Development

| Package | Version | Purpose |
|---|---|---|
| `pestphp/pest` | ^4.4 | Test framework |
| `pestphp/pest-plugin-laravel` | ^4.1 | Laravel test helpers |
| `phpunit/phpunit` | ^12.5 | Test runner |
| `laravel/pint` | ^1.27 | Code style fixer |
| `laravel/pail` | ^1.2.5 | Log viewer |
| `fakerphp/faker` | ^1.23 | Test data generation |
| `nunomaduro/collision` | ^8.6 | Error reporting |
| `mockery/mockery` | ^1.6 | Mocking |

### Infrastructure

| Service | Version | Role |
|---|---|---|
| MariaDB | 11.4.7 | Primary database |
| Redis | latest | Cache, sessions, queues |
| Nginx | latest | Web server / reverse proxy |
| PHP-FPM | 8.3 | PHP process manager |
| Supervisor | latest | Go parser process manager |
| Cloudflare | — | DNS, CDN, DDoS protection |

---

## High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                        Nginx                             │
│          seolinkplace.com / api / admin                  │
└────────────┬───────────────────────────┬─────────────────┘
             │                           │
    ┌────────▼─────────┐      ┌─────────▼────────┐
    │  Laravel 13 App  │      │  Go Parser        │
    │  (Modular Mono)  │      │  Microservice     │
    └────────┬─────────┘      └─────────┬─────────┘
             │                          │
    ┌────────▼──────────┐   ┌──────────▼──────────┐
    │  MariaDB 11.4     │   │  Redis               │
    │  seohands_app     │   │  cache/session/queue │
    └───────────────────┘   └─────────────────────┘
```

---

## Authentication & User Model

### Guards

| Guard | Model | Purpose |
|---|---|---|
| `unified` | `UnifiedUser` | Primary guard — all human users |
| `web` | `User` | Legacy / system use only |

### Role System

A single `UnifiedUser` can hold multiple roles via the `user_roles` pivot table:

```
UnifiedUser ──< UserRole >── role (client | webmaster | performer)
                    │
                    └── Client / Webmaster / PerformerProfile
```

Role adoption is handled by `AddRoleController`. New users (including Google OAuth) are routed to role selection before accessing any cabinet.

### Middleware Stack

| Middleware | Location | Purpose |
|---|---|---|
| `EnsureUnifiedUserAuthenticated` | Core | Base unified guard check |
| `EnsureClientAuthenticated` | Auth | Requires active client role |
| `EnsureWebmasterAuthenticated` | Auth | Requires active webmaster role |
| `EnsureAnyClientAuthenticated` | Auth | Client OR unified with client role |
| `EnsureAnyWebmasterAuthenticated` | Auth | Webmaster OR unified with webmaster role |
| `EnsurePerformerAuthenticated` | Auth | Requires performer role |
| `DetectTenant` | Core | Multi-tenant detection |
| `SetLocale` | Core | UK/EN locale switch |
| `CheckGdprDeleted` | Core | Blocks soft-deleted GDPR users |
| `CheckUserBanned` | Core | Blocks banned users |
| `RedirectIfAuthenticated` | Core | Guest-only route protection |

### Helpers

- `AuthHelper` — static helper for cross-guard user resolution; used everywhere user context is needed regardless of which guard is active
- `CommissionHelper` — commission calculation logic used across Billing and Wallet

---

## Module Map

```
Modules/
├── Affiliate/     — Referral programs, referral tracking, affiliate wallets & withdrawals
├── Articles/      — Article orders, approval flow, ratings, revision system
├── Auth/          — Login, registration, Google OAuth, role selection, middleware
├── Billing/       — Direct payments (USDT), compensation requests, webmaster withdrawals
├── Blog/          — Public bilingual blog with SEO articles and og:image generation
├── Campaigns/     — Campaign management, link orders, daily charge scheduler
├── Core/          — Users, roles, settings, commissions, subscriptions, admin pages
├── Links/         — Link tracking and management
├── Parser/        — Go microservice bridge, link checking, anchor stats
├── Sites/         — Site registry, verification, pages, connections, WHOIS sync
├── Support/       — Support tickets, chat messages, contact requests
├── Tasks/         — Performer task system (claim, complete, auto-approve)
└── Wallet/        — Client wallet (USDT), webmaster wallet, internal transfers
```

---

## Module Detail

### Core
The backbone module. Contains all user/role models, settings infrastructure, subscription system, and admin panel pages.

**Key models:** `UnifiedUser`, `Client`, `Webmaster`, `UserRole`, `ClientProfile`, `WebmasterProfile`, `PerformerProfile`, `CommissionSetting`, `ClientCommissionOverride`, `SubscriptionPlan`, `UserSubscription`, `Setting`, `PlatformSetting`, `BannedDomain`, `PagePrice`, `TenantToken`

**Services:** `EmailService`, `SubscriptionService`, `LocaleService`, `ErrorReportService`, `WpCacheFlushService`

**Filament:** UnifiedUser, Client, Webmaster resources; CommissionSettings; SubscriptionPlans; SettingsPage; StatsOverviewWidget

---

### Auth
Handles all authentication flows including Google OAuth via Socialite (`->stateless()`).

**Key controllers:** `AuthController`, `GoogleAuthController`, `AddRoleController`

**Flow:** New user → role selection → cabinet. Existing user → cabinet switcher (smart redirect based on active roles).

---

### Campaigns
Core business logic for link placement campaigns.

**Key models:** `Campaign`, `CampaignLink`, `CampaignLinkStat`, `CampaignLinkStatusHistory`

**Commands:** `ChargeDailyCampaigns` (idempotent via `last_charged_at`), `ChargeOnclickClicks`, `CleanOldCampaignStats`

**Jobs:** `ReactivatePausedLinks` | **Observers:** `CampaignObserver`, `CampaignLinkObserver`

**Mail:** `OrderCreatedMail`, `OrderApprovedMail`, `OrderRejectedMail`

---

### Sites
Webmaster site registry with verification, page sync, and WHOIS data.

**Key models:** `Site`, `SitePage`, `SiteConnection`, `SiteClientPermission`, `SiteLanguage`

**Commands:** `WhoisSyncCommand` | **Jobs:** `SyncSitePages`

**Observers:** `SiteObserver`, `SitePageObserver`, `SiteConnectionObserver`

---

### Billing
Payment processing and withdrawal flows.

**Gateways** (implement `PaymentGatewayInterface`): `CryptomusGateway`, `NowPaymentsGateway`, `DirectUsdtGateway`

**Key models:** `DirectPayment`, `DirectPaymentSpend`, `InternalTransfer`, `CompensationRequest`, `WebmasterWithdrawal`

**Commands:** `CheckDirectUsdtPayments`, `DirectPaymentsExpire`

---

### Wallet
Client and webmaster wallet management with transaction history.

**Key models:** `Wallet`, `WalletTransaction`, `WebmasterWallet`, `WebmasterTransaction`

**Commission model:** Charged on withdrawal. Config via `CommissionSetting` + per-client `ClientCommissionOverride`.

---

### Articles
Article ordering flow between clients (buyers) and webmasters (writers).

**Flow:** Client orders → webmaster submits → client reviews → approve / reject / request revision

**Key models:** `Article`, `ArticleRating`

**Mail:** `ArticleSubmittedMail`, `ArticleApprovedMail`, `ArticleRejectedMail`

---

### Tasks
Performer task board — claim, complete, auto-approve system.

**Key models:** `Task`, `TaskType`, `TaskTypeTranslation`, `TaskCompletion`, `TaskComplaint`

**Commands:** `AutoApproveCompletions`, `ReleaseExpiredClaims`

**Observers:** `TaskObserver`, `TaskCompletionObserver`

---

### Support
Support ticket system and live chat between users and admin/webmaster.

**Key models:** `SupportTicket`, `SupportTicketMessage`, `ChatMessage`, `ContactRequest`

---

### Affiliate
Referral program with per-webmaster overrides and affiliate wallet.

**Key models:** `ReferralProgram`, `ReferralProgramOverride`, `Referral`, `AffiliateWallet`, `AffiliateTransaction`, `AffiliateWithdrawal`

---

### Parser
Bridge to the Go parser microservice. Handles link health checks, anchor tracking, WP plugin.

**Go microservice:** `parser/` — Supervisor: `/etc/supervisor/conf.d/seolinkplace-parser.conf`

**Key models:** `LinkCheck`, `LinkCheckDiff`, `LinkCheckExternal`, `ParseJob`, `AnchorClick`, `AnchorStat`

**Filament widgets:** `ClicksTrendChartWidget`, `TopAnchorsChartWidget`, `TopSitesChartWidget`

---

### Blog
Public bilingual blog with SEO-optimized articles and automatic og:image generation.

---

### Links
Link tracking and management layer.

---

## Admin Panel (Filament 5)

Admin panel at `/admin`. Resource discovery configured per-module in `AdminPanelProvider`.

Navigation uses `getNavigationGroup()` method pattern. Actions use `Filament\Actions\` namespace.

| Module | Resources |
|---|---|
| Core | UnifiedUsers, Clients, Webmasters, CommissionSettings, SubscriptionPlans, PagePrices, PlatformSettings, BannedDomains, PlatformRules, ApplyRequests, ErrorReports, TenantTokens, UserAppeals, Wallets, ClientCommissionOverrides |
| Campaigns | Campaigns |
| Sites | Sites, SitePages, SiteConnections |
| Articles | Articles |
| Tasks | Tasks, TaskTypes, TaskCompletions, TaskComplaints |
| Support | SupportTickets, ChatMessages, ContactRequests |
| Billing | DirectPayments, WebmasterWithdrawals, CompensationRequests, WebmasterDirectSettings |
| Affiliate | ReferralPrograms, Referrals, AffiliateWithdrawals |
| Blog | BlogPosts |
| Parser | AnchorStats |

---

## Scheduled Commands

| Command | Module | Schedule | Purpose |
|---|---|---|---|
| `ChargeDailyCampaigns` | Campaigns | Daily | Deduct daily campaign fees (idempotent) |
| `ChargeOnclickClicks` | Campaigns | Hourly | Charge onclick/PPC campaigns |
| `CleanOldCampaignStats` | Campaigns | Weekly | Prune old stat records |
| `CheckDirectUsdtPayments` | Billing | Every 5 min | Poll USDT payment confirmations |
| `DirectPaymentsExpire` | Billing | Daily | Expire unpaid invoices |
| `WhoisSyncCommand` | Sites | Daily | Sync WHOIS data for registered sites |
| `AutoApproveCompletions` | Tasks | Daily | Auto-approve old task completions |
| `ReleaseExpiredClaims` | Tasks | Every 30 min | Release stale task claims |
| `ChargeSubscriptions` | Core | Daily | Charge active subscriptions |

---

## Mail System

All mail classes extend `BaseMail` (Core). `BaseMail` handles locale injection without conflicting with `Mailable::$locale`.

| Mail class | Module | Trigger |
|---|---|---|
| `OrderCreatedMail` | Campaigns | New link order |
| `OrderApprovedMail` | Campaigns | Order approved |
| `OrderRejectedMail` | Campaigns | Order rejected |
| `ArticleSubmittedMail` | Articles | Article submitted |
| `ArticleApprovedMail` | Articles | Article approved |
| `ArticleRejectedMail` | Articles | Article rejected |
| `TaskCompletionMail` | Tasks | Task completed |
| `WithdrawalRequestedMail` | Billing | Withdrawal requested |
| `BaseMail` | Core | Base class — locale, layout |

---

## Database Schema

**Engine:** MariaDB 11.4.7 | **Charset:** utf8mb4_unicode_ci | **Total tables:** 80

### Users & Roles

**`unified_users`** — primary user table (guard: `auth('unified')`)
```
id, name, email [UNI], password, remember_token
email_verified_at, status (active|banned|pending)
locale (uk|en), is_trusted
banned_until, ban_reason, warning_count
rules_agreed_at
google_id [UNI], google_email
gdpr_consent_at, gdpr_consent_ip, gdpr_deleted, gdpr_deleted_at
chat_banned_at, created_at, updated_at
```

**`user_roles`** — role pivot (one user, many roles)
```
id, user_id → unified_users
role (client|webmaster|performer)
status (active|suspended)
created_at, updated_at
```

**`clients`** — legacy client profile (linked via user_id)
```
id, name, email [UNI], password, company_name
plan (starter|pro|agency), status (active|suspended|cancelled)
is_adult_verified, birth_date, email_verified_at
chat_banned_at, trial_ends_at, webmaster_id, remember_token
created_at, updated_at
```

**`webmasters`** — legacy webmaster profile (linked via user_id)
```
id, name, email [UNI], password, email_verified_at
status (pending|verified|rejected), plan (free|pro)
freeze_disabled, chat_banned_at, verification_token
client_id, remember_token, created_at, updated_at
```

**`performer_profiles`**
```
id, user_id [UNI] → unified_users
rating (decimal 3,2), completions_count
created_at, updated_at
```

**`client_profiles`**, **`webmaster_profiles`** — extended profile data

**`user_blocks`**, **`user_appeals`** — moderation

---

### Campaigns & Links

**`campaigns`**
```
id, uuid [UNI], user_id, client_id → clients
name, description
status (draft|active|paused|completed|cancelled)
created_at, updated_at
```

**`campaign_links`** — individual link placements
```
id, uuid [UNI], campaign_id, site_id, article_id
donor_url [IDX], onclick_href, target_url
anchor, anchor_before, anchor_after
link_type (dofollow|nofollow)
placement_type (link|onclick|article_once|article_daily)
order_type (place_only|write_only|write_and_place)
price_per_day, price_per_click
clicks_count, clicks_billed
started_at, expires_at
last_charged_at [DATE — idempotency key for ChargeDailyCampaigns]
status (pending|approved|active|paused|expired|cancelled|rejected)
pause_reason, notes, created_at, updated_at
```

**`campaign_link_stats`** — daily stats per link

**`campaign_link_status_history`** — audit trail of status changes

---

### Sites

**`sites`**
```
id, uuid [UNI], user_id, webmaster_id
domain, platform_type, platform_url, followers_count
first_post_published, first_post_url, first_post_required
niche, language, dr, traffic
domain_registered_at, domain_expires_at, spam_score, pages_count
content_type (article|link_insert|both)
price, description, contact
status (active|suspended|rejected)
verification_token, verified_at
link_block_settings (JSON), visibility (public|private)
is_adult, metrics_source, metrics_updated_at
created_at, updated_at, deleted_at (soft delete), deleted_by
```

**`site_pages`** — crawled/synced pages
```
id, site_id → sites, url, title, anchors (JSON)
wp_post_id, post_type (post|page|custom|homepage)
status (publish|draft|private)
link_limit, published_at, synced_at, created_at, updated_at
```

**`site_connections`** — WordPress API connections
```
id, user_id, tenant_token_id, webmaster_id, site_id
wp_url, wp_username, wp_app_password (encrypted)
status (active|error|disconnected), wp_version, pages_count
free_revisions, revision_price
last_sync_at, last_error, created_at, updated_at
```

**`site_languages`**, **`site_client_permissions`** — language tags and per-client access

---

### Wallet & Billing

**`wallets`** — client wallets
```
id, user_id, client_id [UNI]
balance (decimal 10,2), reserved (decimal 10,2)
created_at, updated_at
```

**`wallet_transactions`** — client ledger
```
id, external_id [UNI], wallet_id
gateway, amount, status, balance_after
type (deposit|charge|refund|webmaster_grant|admin_grant)
description
reference_type, reference_id (polymorphic)
created_by, created_at, updated_at
```

**`webmaster_wallets`**
```
id, user_id, webmaster_id [UNI]
balance (decimal 10,2), pending (decimal 10,2)
created_at, updated_at
```

**`webmaster_transactions`** — webmaster ledger
```
id, webmaster_wallet_id
amount (decimal 10,4), balance_after (decimal 10,4)
type (earning|withdrawal|refund|adjustment)
description
reference_type, reference_id (polymorphic)
created_at, updated_at
```

**`webmaster_withdrawals`**
```
id, user_id, webmaster_id
amount, commission (decimal 10,4), commission_pct
method, details, meta (JSON)
status (pending|processing|completed|rejected)
admin_note, processed_at, created_at, updated_at
```

**`direct_payments`** — webmaster direct payment invoices
```
id, uuid [UNI], client_id, webmaster_id
amount, note
status (pending|confirmed|rejected|expired)
confirmed_by (webmaster|admin), confirmed_at, expires_at
created_at, updated_at
```

**`commission_settings`** — tiered commission config (valid_from makes it versioned)
```
id, commission_pct, webmaster_pct, deposit_fee_pct
client_withdrawal_pct, performer_withdrawal_pct, webmaster_withdrawal_pct
min_withdrawal_amount, valid_from [IDX]
created_by, note, created_at, updated_at
```

**`affiliate_wallets`**
```
id, user_id [UNI], balance, total_earned, created_at, updated_at
```

**`direct_payment_spends`**, **`internal_transfers`**, **`compensation_requests`** — related billing records

---

### Articles

**`articles`**
```
id, uuid [UNI], type, user_id, client_id, site_id
title, content (longtext), brief
content_hash, content_updated_at
google_doc_url, published_url
wp_post_id, wp_site_id, wp_published_at, wp_last_synced_at
status (draft|submitted|approved|published|rejected|revision_requested)
project, campaign, notes
revision_count, revision_comment, published_at
created_at, updated_at
```

**`article_ratings`** — client ratings of articles

---

### Tasks

**`tasks`**
```
id, uuid [UNI]
creator_type, creator_id (polymorphic)
title, description, url, task_type_id
reward, budget_reserved
max_completions, per_user_limit, per_user_daily_limit
verification_type (screenshot|url|text_report|none)
verification_instructions, auto_approve_hours, claim_duration_minutes
completions_count
status (pending_review|active|paused|completed|cancelled)
expires_at, created_at, updated_at
```

**`task_completions`**
```
id, uuid [UNI], task_id
performer_type, performer_id (polymorphic)
proof_url, proof_screenshot, comment
status (claimed|pending|approved|rejected)
creator_note, reward_paid
reviewed_at, claimed_at, claim_expires_at
created_at, updated_at
```

**`task_types`** — categories (with i18n via `task_type_translations`)
```
id, slug [UNI], icon_svg
verification_default, sort_order, is_active
created_at, updated_at
```

**`task_complaints`** — dispute resolution

---

### Support

**`support_tickets`**
```
id, uuid [UNI], user_id → unified_users
role, subject
status (open|in_progress|resolved|closed)
priority (low|normal|high)
assigned_to, last_reply_at, created_at, updated_at
```

**`support_ticket_messages`**
```
id, ticket_id, sender_id, sender_role
message (text), is_read
created_at, updated_at
```

**`chat_messages`**, **`contact_requests`** — additional support channels

---

### Affiliate & Referral

**`referral_programs`** — global config (valid_from versioned, like commission_settings)
```
id, referral_pct, duration_days
valid_from [IDX], created_by, note
created_at, updated_at
```

**`referrals`**
```
id, referrer_id, referee_id [UNI], code
created_at, updated_at
```

**`referral_program_overrides`**, **`affiliate_transactions`**, **`affiliate_withdrawals`**

---

### Settings & Config

**`settings`** — key-value feature flags (used via `Setting::get()` with cache)
```
key (PRI), value (text), type, label, created_at, updated_at
```

**`seolinkplace_settings`**, **`platform_settings`**, **`email_settings`** — extended config

---

### System Tables

| Table | Purpose |
|---|---|
| `sessions` | Redis-backed session storage |
| `cache` / `cache_locks` | Laravel cache (Redis) |
| `jobs` / `job_batches` / `failed_jobs` | Queue system |
| `password_reset_tokens` | Password reset flow |
| `migrations` | Migration history |
| `_migration_map` | Custom migration tracking |
| `activity_logs` | Audit log |
| `email_logs` | Sent email history |
| `error_reports` | Frontend/backend error capture |
| `banned_domains` | Domain blacklist |
| `tenant_tokens` | WP plugin authentication tokens |
| `page_prices` | Per-page pricing config |
| `platform_rules` / `rule_comments` | Platform rules with threaded comments |
| `apply_requests` | Webmaster application queue |
| `user_subscriptions` / `subscription_plans` | Subscription system |
| `blog_posts` | Blog content |
| `parse_jobs` | Parser job queue |
| `link_checks` / `link_check_diffs` / `link_check_externals` | Link health monitoring |
| `anchor_clicks` / `site_anchor_stats` | Click and anchor analytics |

---

## Caching Strategy

- `Setting::get()` — cached platform-wide feature flags
- Catalog/site listings — tagged cache with Observer-driven invalidation on model changes
- Heavy stat queries — `Cache::remember()` with TTL
- Session driver: **Redis**
- Cache driver: **Redis**

---

## Infrastructure

```
/data/www/seolinkplace.com/html/     — Laravel app root
/data/www/seolinkplace.com/parser/   — Go parser microservice
/etc/supervisor/conf.d/seolinkplace-parser.conf  — Parser process manager
```

**Services:** Nginx, PHP-FPM 8.3, MariaDB 11.4, Redis, Supervisor

**DNS/CDN:** Cloudflare

**Deployment:** Direct VPS SSH — changes deployed live, no downtime

---

## Key Conventions

| Convention | Rule |
|---|---|
| User-facing strings | Always via `__('file.key')` — never hardcoded |
| Lang files | Always update `lang/uk/` and `lang/en/` simultaneously |
| IDs in UI | Never expose — always use UUID |
| Mobile | Mobile-first; never break mobile when fixing desktop |
| Container max-width | Lists: `max-w-6xl`, Forms: `max-w-2xl` |
| Assets | All JS/CSS/fonts local — no external CDN dependencies |
| Filament navigation | Use `getNavigationGroup()` method (not property) |
| Filament actions namespace | `Filament\Actions\` |
| Feature flags | `Setting::get('key')` |
| Auth resolution | `AuthHelper::` static methods |
| Commission calculation | `CommissionHelper::` static methods |
| OG meta tags | Required on all public-facing pages |
| GDPR | Soft deletion + data export + consent tracking |
| Emojis | Not used anywhere in code, navigation, or UI |
| Tinker | No auth context — use factories or direct model calls |

---

## Test Suite

**29/29 tests passing** — maintained across all 41 development sessions.

```bash
php artisan test
# or
./vendor/bin/pest
```

Tests located in `tests/` — covering core business logic, auth flows, and billing.

---

## Development Sessions Log

| Session | Key Milestone |
|---|---|
| #1–10 | Initial platform build: auth, campaigns, wallets, billing |
| #11–20 | Articles, tasks, support, affiliate, blog |
| #21–30 | Parser microservice, anchor stats, WP plugin |
| #31–35 | Rebrand SEOHands → SEOLinkPlace |
| #36–38 | Performer role, cabinet switcher, Google OAuth fix |
| #39 | Install `nwidart/laravel-modules` + Pest; migrate models |
| #40 | Migrate all Filament resources (201 files) to modules |
| #41 | Migrate Controllers, Commands, Observers, Jobs, Mail |
| #42 | Tests per module, SYSTEM_DESIGN.md, README, Open Core preparation |
