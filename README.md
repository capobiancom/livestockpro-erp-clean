# LivestockPro ERP

**Comprehensive Farm & Livestock Management System**

A full-featured, self-hosted ERP for modern farm operations — built with Laravel 11 + Vue 3 + Inertia.js.

---

## Quick Start

1. Upload files to your web server (set `public/` as document root)
2. Create a MySQL database
3. Visit `https://yourdomain.com/install` to run the Installation Wizard
4. Log in at `https://yourdomain.com/login`

For detailed instructions, see [documentation/index.html](documentation/index.html).

---

## Features

- **Livestock**: Animals, herds, breeds, reproduction, pregnancies, calvings
- **Health**: Events, diseases, treatments, vaccinations, medication tracking
- **Feeding & Inventory**: Feed logs, stock management, FIFO tracking, supplier management
- **Milk Production**: Daily records, sales, invoicing
- **HR & Payroll**: Employees, attendance, leave, payroll runs, payslips
- **Accounting**: Double-entry bookkeeping, P&L, Balance Sheet, Cash Flow
- **Reports**: 15+ analytical reports
- **Multi-Tenant SaaS** *(optional)*: Stripe & bKash payment gateways, subscription plans
- **Single License Mode**: Set `SAAS_MODE=false` for full access without billing

---

## Requirements

- PHP 8.2+
- MySQL 5.7+ (or SQLite)
- Apache / Nginx with mod_rewrite

---

## Server Setup (CLI)

```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Configure environment
cp .env.example .env
# Edit .env with your database and app settings

php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

---

## Documentation

Full documentation is available in [documentation/index.html](documentation/index.html), covering:

- Server requirements
- Web installer walkthrough
- CLI installation
- SaaS vs single-license configuration
- Payment gateway setup (Stripe, bKash)
- Admin and farm owner guides
- Update instructions
- FAQ and changelog

---

## Support

Support is provided via CodeCanyon. Please submit support tickets through the item page.

---

## License

This product is sold under the CodeCanyon Regular License. One license per installation.
Extended License is required for SaaS/subscription-based deployments.
