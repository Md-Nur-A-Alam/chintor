# Chintor — Developer Guide

Read this before changing code. Product decisions live in `public/assets/requirements/chintor-website-requirements.md` (the SRS); this guide covers *how* the code is organised.

## 1. Stack

| Layer | Choice |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Blade + Tailwind CSS 4 + Alpine.js 3 (no SPA) |
| Database | MySQL/MariaDB in production, SQLite in tests |
| Tests | Pest 3 (`php artisan test --compact`) |
| Formatting | Laravel Pint (`vendor/bin/pint --dirty`) |
| Hosting | Hostinger shared hosting + Cloudflare |

## 2. Folder map

| Path | What lives there |
|---|---|
| `app/Http/Controllers/Site` | Public website (thin controllers) |
| `app/Http/Controllers/Admin` | Admin, manager and sales panels |
| `app/Http/Controllers/Admin/Developer` | Developer tools |
| `app/Http/Requests/Admin/Save*Request.php` | Validation **and** `attributesForModel()` mapping |
| `app/Services` | Business rules: `LeadCapture`, `EnrollmentRequests`, `OrderPlacement`, `PaymentVerification`, `StaffNotifier`, `SiteSettings`, `Media/*` |
| `app/Support` | Small helpers: `Localization`, `Money`, `PhoneNumber`, `YouTube`, `TranslatableInput`, `AdminNavigation`, `DatabaseSchema`, `LogReader`, `SqlStatements` |
| `app/Enums` | Statuses with `label()` (Bangla) and `tone()` (badge colour) |
| `app/Models/Concerns` | `HasTranslations`, `HasReference`, `Publishable` |
| `resources/views/site` | Public pages; components in `components/site` |
| `resources/views/admin` | Admin pages; components in `components/admin` |
| `resources/views/components/form` | Form inputs shared by admin and public forms |
| `resources/docs` | This guide and the role guides shown in the admin panel |
| `lang/bn`, `lang/en` | Website text (`site.php`) and validation messages |

## 3. Conventions

- **No hard-coded catalog text.** Programs, courses, prices, pages and contact details come from the database.
- **Translatable columns** are JSON `{"bn": "…", "en": "…"}`. Read them with `$model->translate('title')`; write them with `TranslatableInput::text()` / `::lines()`.
- **Money** is whole taka (`int`). Display it with `Money::taka($amount)`.
- **Phones** are stored in E.164 (`+8801…`). Normalise them with `PhoneNumber::normalize()`.
- **Statuses** are enums; never compare raw strings in views.
- **Public URLs:** Bangla has no prefix and English uses `/en`. Always build links with `Localization::route('name', [...])`.
- **Business logic lives in services**, not controllers or views. Controllers validate, call a service, then redirect.
- Every page must work in **light and dark** themes. Use semantic colour tokens (`bg-surface`, `text-ink`, `text-accent-ink`…), never raw hex.

## 4. Adding an admin CRUD module (template)

1. Migration + model (with `casts()`, `$fillable`, relations) + factory.
2. `app/Http/Requests/Admin/SaveThingRequest.php`: `authorize()` checks a permission, `rules()`, and `attributesForModel()`.
3. Controller with `index`, `create`, `store`, `edit`, `update`, `destroy`. Look at `PageController` for the shortest example.
4. Views `admin/things/index.blade.php` (uses `x-admin.table`) and `form.blade.php` (uses `x-card` + `x-form.*`).
5. Add routes inside the right `can:` group in `routes/web.php`.
6. Add a menu entry in `App\Support\AdminNavigation::SECTIONS`.
7. Add the permission to `PermissionSeeder` if it is new, then run `php artisan db:seed --class=PermissionSeeder`.
8. Add the page to `tests/Feature/PagesRenderTest.php` and write flow tests.

## 5. Roles and permissions

- Roles are seeded by `RoleSeeder`, and permissions (with the roles that hold them) by `PermissionSeeder`.
- `super_admin` and `developer` pass every check (`Gate::before` in `AppServiceProvider`).
- Check permissions with `$user->can('leads.assign')`, `@can`, or the route middleware `can:…`.
- Leads: `Lead::visibleTo($user)` returns all leads for `leads.view_all` holders, otherwise only the user's own.

## 6. Notifications

- All in-app alerts are `App\Notifications\StaffAlert` (database channel).
- Send them through `StaffNotifier` (for example `newLead()`, `paymentSubmitted()`). Recipients are chosen by permission, so new roles receive the right alerts automatically.
- To add an event, add a method to `StaffNotifier` and call it from the service where the event happens.
- The header bell polls `admin.notifications.summary` every 60 seconds.

## 7. Payments (semi-automated)

1. A customer or staff member records a `Payment` (`pending`) with its method and TrxID.
2. `PaymentVerification::verify()` marks it `verified`. Orders are confirmed and their stock reduced; enrollments are activated, a seat is taken, and the lead is marked enrolled.
3. Online gateways come later: add a provider and set `provider`/`gateway_payload` on `payments`.

## 8. Media storage

- `MediaStorage` picks the provider (driver `auto` tries R2, then ImgBB, then local `public/uploads`).
- Credentials are stored encrypted in `settings.secrets` and are never cached.
- In `auto` mode a failed cloud upload falls back to local storage, and the reason is shown to staff.
- ImgBB: `"You have been forbidden to use this website"` (HTTP 403) means ImgBB blocked the network or IP. It is not caused by the key. Try another connection or the live server.

## 9. Developer tools (`/admin/developer`)

| Tool | Notes |
|---|---|
| Database console | Statements are split on `;`. Writes need write mode, your password, and `CHINTOR_DEV_SQL_WRITES=true` (off by default in production). Every statement is logged in `developer_query_logs`. |
| ER diagram | Built live from the schema (`DatabaseSchema::mermaid()`) and rendered with Mermaid 11.4.1 from jsDelivr, on that page only. |
| Error log | Tail of `storage/logs/*.log`, parsed by `LogReader`. |
| Staff access | Profile switch is limited to the `developer` role (checked with `isDeveloper()`, so super admins cannot switch even though `Gate::before` lets them through elsewhere). It stores your id in the session (`impersonator_id`). Password reset generates a one-time password and notifies the user. |

## 9a. Homepage builder and campaigns

- `App\Services\HomePageContent` stores the hero, stats and section list as JSON in `settings` (key `homepage`) and caches it. Empty texts fall back to `lang/{locale}/site.php` (`site.home.*`).
- `site/home.blade.php` renders the hero, then `@include("site.home.{key}")` for each visible section in order. To add a section: add its key to `HomePageContent::SECTIONS`, add `site.home.sections.{key}.title` translations, create `resources/views/site/home/{key}.blade.php` (render nothing when there is no content), and load its data in `HomeController` only when the section is visible.
- `Campaign::running()` = active and inside `starts_at`/`ends_at`. The hero uses running campaigns with `show_in_hero` and an image; the announcement bar uses the first one with `show_in_bar` (view composer in `AppServiceProvider`, cached for 5 minutes, cleared on save).
- Admin-entered links go through `Localization::path()`, which adds the locale prefix to on-site paths.
- The app timezone is `Asia/Dhaka`.
- `homepage.manage` is held by Marketing (plus admins and developers).

### Motion

- Opening animation: `.animate-rise` / `.animate-pop` with `style="--delay: N"` (100 ms steps), `.animate-float`, `.animate-aurora` (CSS in `resources/css/app.css`).
- Scroll reveal: add `data-reveal` (or wrap in `<x-reveal :index="$loop->index">`). Content is hidden only after `resources/js/motion.js` adds `html.motion`, and items already on screen are shown at once. Do not put `data-reveal` on an element that has its own `transition` utility (utilities override the reveal transition); wrap it instead.
- Count-up: `data-count-to`, `data-count-decimals`, `data-count-grouping`, `data-count-bangla` (see `site/home/stats.blade.php`).
- Lanes: `<x-marquee :reverse="true" :duration="60">`. Content must be wider than the screen to loop without a gap, so repeat short lists (repeats get `aria-hidden` and `tabindex="-1"`).
- Everything respects `prefers-reduced-motion`.

## 9b. Hero banner, learning videos and Exam Arena

- Hero: `HomeController::heroBanner()` picks a running campaign with `banner_url` first, then the homepage `hero.layout = banner`; otherwise the split hero (`site/home/hero.blade.php`). Overlay CSS comes from `App\Support\HeroBanner` (sizes and alignments are constants there).
- `learning_topics` is a two-level tree (subject → topic) shared by `learning_videos` and `questions`. Public filters use slugs.
- `App\Services\ExamArena` starts sittings (`startExam`, `startPractice`) and grades them (`submit`). Limits live in `config('chintor.arena')`. Attempts are addressed by ULID; answers are `{question_id: option_index}`.
- An attempt with a phone creates a lead through `LeadCapture` (`LeadType::ExamArena`).
- Permission: `learning.manage`.

## 10. Useful commands

```bash
php artisan migrate                      # apply migrations
php artisan db:seed                      # roles + permissions
php artisan chintor:create-super-admin   # first admin (prompts for details)
php artisan test --compact               # all tests
vendor/bin/pint --dirty                  # format changed PHP files
npm run build                            # production assets
```

## 11. Deployment checklist (Hostinger)

1. Set `APP_ENV=production`, `APP_DEBUG=false`, a strong `APP_KEY`, and the database credentials in `.env`.
2. Run `composer install --no-dev --optimize-autoloader`, then `php artisan migrate --force`, then `php artisan db:seed --force`.
3. Run `npm run build` locally and upload `public/build` (shared hosting has no Node).
4. Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache`.
5. Point the document root at `public/`. Move `public/assets/requirements` and the PSD/AI logo sources out of `public/`.
6. Remove the demo staff accounts and demo content. Set real payment numbers and contact details.
7. Keep `CHINTOR_DEV_SQL_WRITES` unset (off). Take a database backup before any manual change.
