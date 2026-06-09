<p align="center">
  <img src="https://seolinkplace.com/images/og-home.png" alt="SEOLinkPlace" width="800">
</p>

<h1 align="center">SEOLinkPlace Community Edition</h1>

<p align="center">
  <strong>Open source маркетплейс для розміщення лінків — self-hosted, модульний, production-ready.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13-red?style=flat-square&logo=laravel" alt="Laravel 13">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/Filament-5-f59e0b?style=flat-square" alt="Filament 5">
  <img src="https://img.shields.io/badge/license-MIT-green?style=flat-square" alt="MIT">
</p>

<p align="center">
  <a href="./README.md">🇬🇧 Read in English</a>
</p>

---

## Що це?

SEOLinkPlace Community Edition — це open source ядро платформи [seolinkplace.com](https://seolinkplace.com), B2B маркетплейсу для SEO-розміщення лінків.

Цей репозиторій містить **фундамент** для запуску власного маркетплейсу:

- Мульти-рольова система користувачів (клієнти, вебмастери, виконавці)
- Реєстр сайтів з верифікацією домену
- Адмін-панель на Filament 5
- Двомовний інтерфейс (українська / англійська)
- Google OAuth
- Система тікетів підтримки
- Публічний блог

## Архітектура

Побудовано як **модульний моноліт** на базі [nwidart/laravel-modules](https://nwidart.com/laravel-modules). Кожен модуль самодостатній — має власні моделі, контролери, Filament ресурси, роути і міграції.

```
Modules/
├── Core/      — Користувачі, ролі, автентифікація, адмін-панель
├── Auth/      — Логін, реєстрація, Google OAuth
├── Sites/     — Реєстр сайтів і верифікація доменів
├── Blog/      — Публічний двомовний блог
└── Support/   — Система тікетів підтримки
```

## Стек технологій

| Шар | Технологія |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Адмін-панель | Filament 5 |
| База даних | MariaDB 11+ / MySQL 8+ |
| Кеш / Сесії | Redis |
| Веб-сервер | Nginx + PHP-FPM |

## Вимоги

- PHP 8.4+
- MariaDB 11+ або MySQL 8+
- Redis
- Composer 2
- Node.js 20+

## Встановлення

```bash
# Клонувати
git clone https://github.com/seolinkplace/community.git
cd community

# Встановити PHP залежності
composer install

# Налаштування середовища
cp .env.example .env
php artisan key:generate

# Відредагуйте .env — вкажіть DB_*, REDIS_*, MAIL_*, APP_NAME, APP_URL

# Запустити міграції
php artisan migrate

# Зібрати frontend
npm install && npm run build

# Права на директорії
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### Перший адмін

```bash
php artisan make:filament-user
```

Потім відкрийте `/admin` у браузері.

## Конфігурація

Всі налаштування платформи керуються через `APP_NAME` і `APP_URL` у файлі `.env`. Назва застосунку використовується скрізь автоматично — жодного захардкодженого брендингу.

### Google OAuth (опціонально)

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
```

### hCaptcha (опціонально)

```env
HCAPTCHA_SITE_KEY=your-site-key
HCAPTCHA_SECRET_KEY=your-secret-key
```

---

## Комерційні модулі

Community Edition — це відкрите ядро. Наступні модулі розширюють його до повноцінного production маркетплейсу.

| Модуль | Опис | Ціна |
|---|---|---|
| **AiContent** | AI-генерація контенту для вебмастерів. Шаблони промптів з динамічними змінними, інтеграція Gemini 2.5 Flash, планувальник публікацій, історія ревізій з посимвольним diff. | **$149** |
| **Billing** | Платіжна система з Cryptomus, NOWPayments і USDT TRC20 з коробки. Чистий інтерфейс провайдера — будь-який сервіс з API (Stripe, PayPal, LiqPay та ін.) підключається без зміни ядра. | **$129** |
| **Wallet** | Гаманці клієнтів, вебмастерів і виконавців. Історія транзакцій, управління балансом, система виведення коштів. | **$129** |
| **Campaigns** | Управління кампаніями на стороні клієнта. Замовлення розміщення лінків, щоденне списання з балансу гаманця, аналітика кампаній. | **$99** |
| **Articles** | Workflow замовлення статей між клієнтами і вебмастерами. Клієнт створює замовлення, вебмастер пише і здає, клієнт підтверджує. | **$89** |
| **Parser** | Go мікросервіс для автоматичної перевірки лінків. Обходить цільові сторінки за розкладом, фіксує наявність лінку, призупиняє розміщення після кількох невдалих перевірок. | **$89** |
| **Affiliate** | Реферальна програма з відстеженням комісій, реферальними посиланнями і автоматичними виплатами на гаманець партнера. | **$69** |
| **Tasks** | Дошка задач для виконавців. Типи задач керуються з БД, логіка авто-підтвердження, виплата винагороди після виконання. | **$49** |

### Повний пакет

> **Всі 8 модулів — $599** *(економія $203 порівняно з купівлею окремо)*

---

## Чому саме такі ціни?

Розробка цих модулів з нуля за допомогою найнятого розробника коштує приблизно **$10,000–14,000**:

| Модуль | Оцінка годин | Вартість @ $40/год |
|---|---|---|
| AiContent | 60–80 год | $2,400–3,200 |
| Billing + Wallet | 50–70 год | $2,000–2,800 |
| Campaigns | 40–60 год | $1,600–2,400 |
| Articles | 30–40 год | $1,200–1,600 |
| Parser (Go) | 30–40 год | $1,200–1,600 |
| Affiliate | 20–30 год | $800–1,200 |
| Tasks | 15–20 год | $600–800 |
| **Разом** | **245–340 год** | **$9,800–13,600** |

Повний пакет за **$599** — це економія ~95% порівняно з вартістю власної розробки.

Для порівняння: місячна підписка на аналогічну hosted платформу (Collaborator, Getfound, Miralinks) коштує більше, ніж весь пакет — і код при цьому вам не належить.

---

## Послуга встановлення і налаштування

Хочете щоб все налаштували за вас? Ми надаємо послугу professional deployment:

| Послуга | Що включено | Ціна |
|---|---|---|
| **Встановлення Community Edition** | Налаштування VPS, Nginx + PHP-FPM + Redis, SSL, деплой, конфігурація `.env`, перший адмін, smoke testing | **$799** |
| **Встановлення Enterprise** | Все вище + всі комерційні модулі, queue worker (Supervisor), деплой Go парсера, налаштування платіжного провайдера | **$1,499** |

Зв'язатись через [seolinkplace.com/contact](https://seolinkplace.com/contact).

---

## Документація

- [INSTALL.md](./INSTALL.md) — детальний гайд з встановлення
- [SYSTEM_DESIGN.md](./SYSTEM_DESIGN.md) — повна документація архітектури

## Live Demo

На [seolinkplace.com](https://seolinkplace.com) працює повна Enterprise Edition.

## Ліцензія

MIT License — деталі у файлі [LICENSE](./LICENSE).

Community Edition — вільна для використання, модифікації і поширення.
Комерційні модулі є пропрієтарними і потребують окремої ліцензії.
