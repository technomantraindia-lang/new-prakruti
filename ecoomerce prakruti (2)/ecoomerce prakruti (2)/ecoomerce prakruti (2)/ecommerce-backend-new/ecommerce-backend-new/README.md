# E-Commerce Backend — Laravel WooCommerce Integration Engine

High-performance, secure Laravel backend infrastructure providing robust e-commerce operations, full WooCommerce bidirectional synchronization, role-based administration, API V1 endpoints for storefront developers, analytics reporting, and queue management.

---

## 📋 Project Overview

This backend serves as the core business, inventory, payment, order fulfillment, and WooCommerce synchronization engine. Front-end developers interface with the clean `/api/v1` layer using Sanctum API tokens.

### Key Backend Responsibilities
- **Catalog & Inventory Management**: Additive product structures, categories, subcategories, brands, attributes, variations, and real-time inventory reservation logs without negative stock risk.
- **Order & Fulfillment Operations**: Advanced multi-parameter order filtering, validated bulk status state transitions (`pending -> processing -> packed -> shipped -> delivered`), and invoice generation.
- **WooCommerce Integration**: Bidirectional REST sync client with conflict resolution, idempotency protection, and signature-verified webhooks.
- **Reporting & Exports**: Date-range filtered reporting across Sales, Orders, Products, Customers, Inventory, Payments, Refunds, Returns, GST/HSN summary, and Profit/Margin calculations with CSV exports.
- **System Health & Backups**: Super Admin system health dashboard (`/admin/system-health`), failed job recovery (`/admin/system/failed-jobs`), and zip backup management (`/admin/system/backups`).
- **Security & Authorization**: Granular Spatie-style role-based permissions (`Super Admin`, `Admin`, `Customer`), API rate-limiting (`60 req/min`), Sanctum token auth, and sensitive field masking (`cost_price`, API secrets).

---

## 💻 Requirements & Prerequisites

- **PHP Version**: `^8.3` (Local environment tested on PHP 8.5)
- **Framework**: `Laravel 13.x`
- **Database**: `MySQL 8.0+` or `SQLite` (for testing)
- **Node.js**: `v18+` & `npm`

---

## 🚀 Environment & Initial Setup

1. **Clone repository and install Composer dependencies**:
   ```bash
   composer install
   ```

2. **Environment File Setup**:
   Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Required Environment Variable Names**:
   ```ini
   APP_NAME="Madhav E-Commerce Backend"
   APP_ENV=production
   APP_KEY=
   APP_DEBUG=false
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ecommerce_backend
   DB_USERNAME=root
   DB_PASSWORD=

   QUEUE_CONNECTION=database
   SESSION_DRIVER=file
   CACHE_STORE=file

   WOOCOMMERCE_URL=https://your-woocommerce-store.com
   WOOCOMMERCE_CONSUMER_KEY=ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   WOOCOMMERCE_CONSUMER_SECRET=cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   WOOCOMMERCE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```

4. **Database Migration**:
   > [!CAUTION]
   > NEVER run `php artisan migrate:fresh` in production!
   
   Execute safe additive migrations:
   ```bash
   php artisan migrate
   ```

5. **Assets Build**:
   ```bash
   npm install
   npm run build
   ```

---

## ⚙️ Queue Worker & Scheduler Configuration

External WooCommerce sync jobs, notification dispatches, and heavy report exports are processed asynchronously via queues.

### Manual Worker Execution (Development)
```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=120
```

### Production Supervisor Configuration Example (`/etc/supervisor/conf.d/laravel-worker.conf`)
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work database --sleep=3 --tries=3 --timeout=120
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

---

## 🔌 WooCommerce API & Webhook Setup

### 1. REST API Credentials
In WooCommerce Admin -> Settings -> Advanced -> REST API:
- Create a key with **Read/Write** permissions.
- Set `WOOCOMMERCE_URL`, `WOOCOMMERCE_CONSUMER_KEY`, and `WOOCOMMERCE_CONSUMER_SECRET` in `.env`.

### 2. Webhook Endpoint
Configure Webhook Delivery URL in WooCommerce:
- **Delivery URL**: `https://your-domain.com/api/webhooks/woocommerce`
- **Secret**: Set `WOOCOMMERCE_WEBHOOK_SECRET` in `.env`.
- **Required Webhook Topics**:
  - `product.created`, `product.updated`, `product.deleted`
  - `order.created`, `order.updated`
  - `customer.created`, `customer.updated`

---

## 📡 API V1 Documentation Summary (`/api/v1`)

All public product & catalog endpoints respond with standard envelopes and apply rate-limiting:

- `GET /api/v1/products`: Paginated product listing with filters (`search`, `category`, `subcategory`, `brand`, `min_price`, `max_price`, `featured`, `in_stock`, `attribute`, `attribute_value`, `sort`, `page`, `per_page`).
- `GET /api/v1/products/{slug}`: Single product details (Cost price and internal notes strictly masked).
- `GET /api/v1/products/{product}/variations`: Product variations list.
- `GET /api/v1/categories` & `GET /api/v1/categories/{slug}`
- `GET /api/v1/brands`
- `GET /api/v1/attributes`
- `POST /api/v1/register`: Public customer registration (Always assigns `Customer` role name).
- `POST /api/v1/login`: Customer authentication returning Bearer Sanctum API token.
- `POST /api/v1/logout` (Bearer Auth): Revokes active token.
- `GET /api/v1/me` & `PUT /api/v1/me` (Bearer Auth)
- `GET /api/v1/orders` & `GET /api/v1/orders/{id}` (Bearer Auth)

---

## 🛡️ Roles & Permissions Matrix

| Role | Access Scope |
| :--- | :--- |
| **Super Admin** | Full access to Admin Panel, System Health Dashboard (`/admin/system-health`), System Failed Jobs (`/admin/system/failed-jobs`), System Backups (`/admin/system/backups`), Settings, and User Management. |
| **Admin / Staff** | Permission-gated access (`products.view`, `orders.view`, `orders.edit`, `reports.view`, `customers.view`). Restricted from Super Admin health/backup pages. |
| **Customer** | Front-end & API V1 customer account access only. Restricted from `/admin` (Returns `403 Forbidden`). |

---

## 💾 Backups & Recovery Instructions

Backups are managed via Super Admin interface at `/admin/system/backups` or manually stored in non-public directory `storage/app/backups/`.

- **Create Backup**: Trigger zip snapshot creation containing database state and manifest metadata.
- **Download Backup**: Securely download backup zip file (Super Admin authorized only).
- **Activity Logging**: All backup operations are recorded in `activity_logs`.

---

## 🧪 Testing Commands

Execute full automated test suite using PHP 8.3+:

```bash
# Run all unit and feature regression tests
php artisan test

# Run test suite with compact summary
php artisan test --compact
```

---

## 🔒 Security & Hardening Verification

- `.env` is ignored by Git and contains no hardcoded credentials.
- WooCommerce consumer keys/secrets are stored strictly server-side and never exposed in Blade, JS, or public API Resources.
- Cost price (`cost_price`), profit margins, and supplier internals are hidden from public API responses.
- Webhook signature verification and idempotency checks prevent duplicate order creation.
- Login and API endpoints are protected by Laravel `RateLimiter` throttles.
