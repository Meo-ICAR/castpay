---
description: Repository Information Overview
alwaysApply: true
---

# Castpay Information

## Summary
Castpay is a multi-company SaaS web application built using Laravel 12 and Filament 4.4. It is designed to manage service payments for multiple companies, each maintaining their own price listings for their customers. It integrates Stripe Cashier for payment processing and subscription management, and features role-based access control for service visibility.

## Structure
- **app/Models/**: Core models including `Company` (Tenant), `User`, `Service`, `Price`, and `Customer`.
- **app/Filament/Resources/**: Admin resources for managing Services, Prices, and Customers, scoped to the current tenant.
- **database/migrations/**: Multi-tenant schema migrations with proper dependency ordering.
- **tests/Feature/**: Automated tests for multi-tenancy and role-based service visibility.

## Language & Runtime
**Language**: PHP  
**Version**: ^8.2  
**Build System**: Vite (Frontend), Composer (Backend)  
**Package Manager**: Composer, npm

## Dependencies
**Main Dependencies**:
- `laravel/framework`: ^12.0
- `filament/filament`: ^4.4 (Multi-tenant Admin)
- `laravel/cashier`: ^16.1 (Stripe Integration)

## Build & Installation
```bash
composer setup
php artisan migrate
npm install && npm run build
```

## Testing
**Framework**: PHPUnit  
**Test Location**: `tests/`  
**Run Command**:
```bash
vendor/bin/phpunit
```
