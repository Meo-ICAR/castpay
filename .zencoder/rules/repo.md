---
description: Repository Information Overview
alwaysApply: true
---

# CastPay Repository Information

## Summary
CastPay is a multi-tenant SaaS application tailored for the casting industry. Built with Laravel 12 and Filament 4.4, it manages companies (tenants), services, prices, and customers. It integrates with Stripe via Laravel Cashier for payment synchronization and features a role-based access control system specifically designed for casting roles (Actor, Worker, etc.).

## Structure
- **app/**: Core application logic, including Models, Services, and Providers.
- **app/Filament/**: Configuration for the Filament admin panel, including resources for Customers, Prices, and Services.
- **app/Models/**: Data models including `Company` (Tenant), `User`, `Role`, `Service`, `Price`, and `Customer`.
- **app/Services/**: Business logic services, notably `StripeSyncService` for Stripe integration.
- **database/**: Database schema migrations and seeders (including `CastingItalianSeeder`).
- **resources/**: Frontend assets managed by Vite and Tailwind CSS.
- **routes/**: Application routing definitions.
- **tests/**: Automated tests (Feature and Unit).

## Language & Runtime
**Language**: PHP  
**Version**: ^8.2  
**Framework**: Laravel 12.0  
**Admin Panel**: Filament 4.4  
**Build System**: Vite  
**Package Manager**: Composer (PHP), NPM (JavaScript)

## Dependencies
**Main Dependencies**:
- `filament/filament`: ^4.4 (Admin interface)
- `laravel/cashier`: ^16.1 (Stripe integration)
- `laravel/framework`: ^12.0 (Core framework)
- `barryvdh/laravel-dompdf`: ^3.1 (PDF generation)

**Development Dependencies**:
- `phpunit/phpunit`: ^11.5.3 (Testing)
- `laravel/sail`: ^1.41 (Docker environment)
- `laravel/pint`: ^1.24 (Code styling)
- `tailwindcss`: ^4.0.0 (CSS framework)

## Build & Installation
```bash
# Full project setup
composer setup

# Manual installation
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate
```

## Main Files & Resources
- **Admin Panel Configuration**: `app/Providers/Filament/AdminPanelProvider.php`
- **User & Multi-tenancy Logic**: `app/Models/User.php`
- **Stripe Integration**: `app/Services/StripeSyncService.php`
- **Entry Point**: `public/index.php`
- **Configuration**: `.env`, `config/*.php`

## Testing
**Framework**: PHPUnit  
**Test Location**: `tests/Feature`, `tests/Unit`  
**Naming Convention**: `*Test.php`  
**Configuration**: `phpunit.xml`

**Run Command**:
```bash
php artisan test
# OR
composer test
```
