# Code Review: SEOLinkPlace Community Edition

**Дата:** 2026-05-01  
**Версія:** Laravel 13 / PHP 8.3  
**Охоплення:** Modules/Auth, Modules/Sites, Modules/Core, Modules/Support, routes/

---

## Виправлено у цьому PR

### 🔴 Критичні

#### 1. Site + TenantToken без транзакції
**Файл:** `Modules/Sites/app/Http/Controllers/Webmaster/SiteController.php`

`Site::create()` і `TenantToken::create()` виконувалися окремо. Якщо створення токена падало — сайт залишався в БД без токена, що порушувало цілісність даних.

**Виправлення:** обидва виклики обгорнуті в `DB::transaction()`.

---

#### 2. Валідація `domain` не перевіряла формат
**Файл:** `Modules/Sites/app/Http/Controllers/Webmaster/SiteController.php`

Правило `'domain' => 'required|string|max:255'` дозволяло зберегти будь-який рядок, включно з пробілами, SQL-синтаксисом тощо.

**Виправлення:** додано regex `^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$` в обох методах `store()` та `update()`.

---

#### 3. `first_post_url` не прив'язаний до домену сайту
**Файл:** `Modules/Sites/app/Http/Controllers/Webmaster/SiteController.php`

Вебмайстер міг вказати URL чужого домену — жодної перевірки приналежності не було.

**Виправлення:** кастомне правило валідації порівнює `parse_url($value, PHP_URL_HOST)` з `$site->domain` (з врахуванням `www.`).

---

#### 4. `GdprDeletionService` — неправильна колонка у `support_ticket_messages`
**Файл:** `Modules/Core/app/Services/GdprDeletionService.php`

Запит `->where('user_id', $userId)` не знаходив жодного запису, бо таблиця використовує колонку `sender_id`. Це означало, що при GDPR-видаленні тексти повідомлень підтримки не анонімізувались.

**Виправлення:** замінено `user_id` на `sender_id`.

---

#### 5. Race condition при реєстрації
**Файл:** `Modules/Auth/app/Http/Controllers/AuthController.php`

Між перевіркою `UnifiedUser::where('email')->first()` і `UnifiedUser::create()` паралельний запит міг створити користувача з тим самим email — виключення `UniqueConstraintViolationException` не оброблялося і повертало 500.

**Виправлення:** `create()` обгорнутий у `try/catch UniqueConstraintViolationException`.

---

### 🟠 Важливі

#### 6. N+1 запит у `hasRole()`
**Файл:** `Modules/Core/app/Models/UnifiedUser.php`

Кожен виклик `$user->hasRole('x')` виконував окремий SQL-запит, навіть якщо relation `roles` вже була завантажена через `with('roles')` або `loadMissing('roles')`.

**Виправлення:** перевірка `$this->relationLoaded('roles')` — якщо relation вже в пам'яті, використовується колекція без запиту в БД.

---

#### 7. `HcaptchaService` — порушення Dependency Injection
**Файл:** `Modules/Auth/app/Http/Controllers/AuthController.php`

`new HcaptchaService()` в тілі методу обходить IoC-контейнер Laravel: неможливо підмінити в тестах, не резолвляться залежності класу.

**Виправлення:** constructor injection через `__construct(private readonly HcaptchaService $hcaptcha)`.

---

#### 8. `SubscriptionService::chargeAllDue()` — потенційний memory overflow
**Файл:** `Modules/Core/app/Services/SubscriptionService.php`

`->get()` завантажував всі підписки одразу. При великій кількості записів це призводить до надмірного споживання пам'яті в Artisan-команді.

**Виправлення:** замінено на `->chunk(100, ...)`.

---

#### 9. Мертвий код у `ref_code`
**Файл:** `Modules/Auth/app/Http/Controllers/AuthController.php`

`$data['ref_code'] ?? request('ref_code')` — після `validate()` ключ `ref_code` гарантовано є в `$data` (nullable), тому fallback `request('ref_code')` ніколи не виконувався.

**Виправлення:** прибрано зайвий `?? request('ref_code')`.

---

#### 10. Open redirect у language switcher
**Файл:** `routes/web.php`

`redirect()->back()` повертає на `Referer`-заголовок без додаткового захисту від відкритого перенаправлення на зовнішні домени.

**Виправлення:** замінено на `redirect(url()->previous('/'))` — Laravel гарантує що `url()->previous()` поверне внутрішній URL або вказаний fallback.

---

#### 11. `email` валідація без RFC-перевірки
**Файли:** `Modules/Auth/app/Http/Controllers/AuthController.php`

Правило `email` без параметрів використовує спрощену перевірку формату. `email:rfc` дотримується RFC 5321/5322.

**Виправлення:** змінено на `'email:rfc'` у `login()` і `register()`.

---

### 🟡 Середній пріоритет

#### 12. Відсутній складений індекс на `support_tickets`
**Файл:** `database/migrations/2026_05_01_000001_add_performance_indexes.php` (новий)

Запити `SupportTicket::where('user_id')->orderByDesc('status')` виконували full scan по FK-індексу і потребували додаткової сортування.

**Виправлення:** новий compound index `(user_id, status)`.

---

#### 13. `priority` не є обов'язковим полем
**Файл:** `Modules/Support/app/Http/Controllers/SupportTicketController.php`

`'priority' => 'in:low,normal,high'` без `required` означало, що `null` проходив валідацію, а далі збережений `priority` залежав від `$request->priority ?? 'normal'`.

**Виправлення:** додано `required`.

---

#### 14. `declare(strict_types=1)` відсутній
**Файли:** всі змінені PHP-файли

Додано `declare(strict_types=1)` згідно з вимогами code style проекту.

---

## Не входить у цей PR (до розгляду)

| Проблема | Причина відкладення |
|----------|---------------------|
| Form Request класи для складних форм | Потребує рефакторингу всіх контролерів |
| Route Model Binding + Policies для SupportTicket | Зміна підпису методів — breaking change для маршрутів |
| CI/CD pipeline (GitHub Actions) | Окреме завдання |
| Дублювання support-маршрутів (×3) | Потребує окремого рефакторингу routes |
| Мішанина `App\Models` і `Modules\*\Models` | Архітектурне рішення на рівні проекту |
