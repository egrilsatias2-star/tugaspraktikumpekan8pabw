# SiPustaka

SiPustaka is a Laravel 12 library app built for Praktikum 8.5. It uses HMVC modules for book, loan, and user management, with authentication outside those modules.

## Features

- Register, sign in, and sign out. Public registration always creates a regular member.
- Admin-only synchronous book CRUD, search, ISBN uniqueness, cover upload, and stock management.
- AJAX loan CRUD with server-side validation, borrower scoping, proof uploads, and transactional stock changes.
- Admin UserManagement module with role authorization; an admin cannot delete or demote their own account.
- Uploaded covers and loan proofs are stored on Laravel's public disk.

## Run locally

Requirements: PHP 8.2+, Composer, Node.js/npm, and SQLite or MySQL.

```sh
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install --legacy-peer-deps
npm run build
php artisan serve
```

Open `http://127.0.0.1:8000`. For the default SQLite setup, Laravel uses `database/database.sqlite`; create an empty file there if it does not exist. MySQL can be selected in `.env` instead.

The development seeder creates:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@sipustaka.test` | `password` |
| Member | `anggota@sipustaka.test` | `password` |

These are local demonstration credentials only. Change them before exposing the application beyond a development environment. The seeder adds five sample books and five active sample loans.

## Checks

```sh
php artisan test
```

For the AJAX loan module, open DevTools → Network and filter Fetch/XHR. Validation failures return HTTP 422 with field-level error messages; unauthorized admin pages return HTTP 403.
