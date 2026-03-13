# GitHub Copilot Instructions

## Project Overview

This is a **Laravel 12 + Vue 3 + Inertia.js** full-stack social media/blog platform. Users can register, create posts, like posts, and comment on posts. The application uses a single-page application (SPA) architecture powered by Inertia.js without a separate REST API.

## Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | PHP / Laravel | ^8.2 / ^12.0 |
| Frontend | Vue.js | ^3.5 |
| SPA Bridge | Inertia.js | ^2.0 |
| Build Tool | Vite | ^7.0 |
| CSS Framework | Tailwind CSS | ^4.0 |
| UI Components | Bootstrap + AdminLTE | ^5.3 / ^4.0-rc |
| Database | SQLite (dev) / MySQL (prod) | — |
| Testing | PHPUnit | ^11.5 |
| Code Style | Laravel Pint | ^1.24 |

## Repository Structure

```
ingcoexamv1/ingcoexamv1/   # Main Laravel application root
├── app/
│   ├── Http/
│   │   ├── Controllers/   # LoginController, RegisterController,
│   │   │                  # DashboardController, ProfileController, BlogController
│   │   └── Middleware/
│   ├── Models/            # User, Post, Comment, PostLike
│   └── Providers/
├── resources/
│   ├── js/
│   │   ├── app.js         # Inertia bootstrap
│   │   ├── Pages/         # Vue page components (Inertia pages)
│   │   └── Components/    # Shared Vue components
│   ├── css/app.css        # Tailwind entry point
│   └── views/
│       └── app.blade.php  # Root Blade template (Inertia shell)
├── routes/
│   ├── web.php            # All application routes
│   └── console.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── tests/
│   ├── Unit/
│   └── Feature/
└── public/
```

## Key Domain Models

- **User** — `name`, `email`, `password`, `profile_image`, `age`, `address`, `contact_number`
- **Post** — `user_id`, `content`, likes count (aggregated from PostLike)
- **Comment** — belongs to a Post and a User
- **PostLike** — pivot between User and Post for like/unlike toggling

## Routing Conventions

Routes are defined in `routes/web.php` using Laravel's named routes:

| Route | Method | Auth | Description |
|-------|--------|------|-------------|
| `/` | GET | Guest | Login page |
| `/register` | GET/POST | Guest | Registration |
| `/dashboard` | GET | Auth | Post feed |
| `/profile` | GET/POST | Auth | View/edit profile |
| `/posts` | POST | Auth | Create post |
| `/posts/{post}` | PUT/DELETE | Auth | Update/delete post |
| `/posts/{post}/like` | POST | Auth | Toggle like |
| `/posts/{post}/comments` | POST | Auth | Add comment |
| `/logout` | POST | Auth | Logout |

## Inertia.js Conventions

- Backend controllers return `Inertia::render('PageName', $props)` instead of Blade views.
- Page components live in `resources/js/Pages/`.
- Shared data (auth user, flash messages) is passed via `HandleInertiaRequests` middleware.
- Use `router.visit()` or `<Link>` from `@inertiajs/vue3` for client-side navigation.

## Vue 3 Conventions

- Use the **Composition API** (`<script setup>`) for all new components.
- Props received from Inertia are defined with `defineProps()`.
- Reactive state uses `ref()` and `reactive()`.
- Place reusable components in `resources/js/Components/`.
- Page-level components go in `resources/js/Pages/`.

## PHP / Laravel Conventions

- Follow **PSR-12** coding style enforced by **Laravel Pint** (`./vendor/bin/pint`).
- Use **Form Request** classes for validation logic (keep controllers lean).
- Authorize actions using **Policies** or inline `$this->authorize()`.
- Eloquent relationships should be defined in the Model classes.
- Use `resource` controllers where applicable.

## Development Workflow

```bash
# Install dependencies
composer install
npm install

# Run database migrations
php artisan migrate

# Start development servers (run in separate terminals)
php artisan serve       # Laravel backend  → http://localhost:8000
npm run dev             # Vite HMR server

# Build for production
npm run build

# Run tests
php artisan test

# Fix code style
./vendor/bin/pint
```

## Testing Guidelines

- **Feature tests** cover HTTP routes and controller behavior (`tests/Feature/`).
- **Unit tests** cover isolated model/service logic (`tests/Unit/`).
- Use Laravel's built-in `RefreshDatabase` trait to reset the database between tests.
- Use **Factories** and **Seeders** for test data.
- Run a specific test: `php artisan test --filter TestClassName`.

## Code Style Guidelines

- PHP: Laravel Pint (PSR-12 variant). Run `./vendor/bin/pint` before committing.
- JavaScript/Vue: follow Vue 3 style guide (https://vuejs.org/style-guide/).
- Use **single quotes** in PHP and JavaScript strings.
- Blade templates: indent with 4 spaces.
- Vue templates: indent with 2 spaces.

## Security Notes

- All auth-required routes are protected by the `auth` middleware.
- Guest-only routes (login, register) use the `guest` middleware.
- CSRF protection is enabled globally via Laravel's default middleware stack.
- User-uploaded profile images should be validated for MIME type and stored via `Storage::disk('public')`.
- Never expose sensitive `.env` values; use `config()` helpers in code.
