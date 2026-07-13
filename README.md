# 🚀 Laravel Multi-Tenant SaaS Boilerplate

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-red?style=flat-square&logo=laravel" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Multi--Tenant-stancl/tenancy-green?style=flat-square" alt="Tenancy">
  <img src="https://img.shields.io/badge/Payments-Stripe+Razorpay-purple?style=flat-square" alt="Payments">
  <img src="https://img.shields.io/badge/License-MIT-yellow?style=flat-square" alt="MIT">
</p>

> Complete Laravel 11 SaaS starter kit — Save 200+ hours of development time!

---

## ✅ Features

| Feature | Status |
|---------|--------|
| Multi-Tenancy (isolated DB per tenant) | ✅ |
| Login / Register / Email Verification | ✅ |
| Roles & Permissions (Owner, Admin, Member) | ✅ |
| Stripe Payment Integration | ✅ |
| Razorpay Payment Integration (India) | ✅ |
| Subscription Plans (Free, Basic, Pro) | ✅ |
| Super Admin Dashboard | ✅ |
| Tenant Dashboard | ✅ |
| Team Management (Invite, Remove, Role change) | ✅ |
| Profile Settings (Name, Password, Avatar) | ✅ |
| Activity Logs (Audit Trail) | ✅ |
| REST API (Laravel Sanctum) | ✅ |
| Dark / Light Mode | ✅ |
| Email Notifications | ✅ |
| Install Wizard (1 command setup) | ✅ |

---

## 🛠️ Tech Stack

- **Laravel 11** — PHP Framework
- **stancl/tenancy v3.10** — Multi-tenancy
- **spatie/laravel-permission** — Roles & Permissions
- **spatie/laravel-activitylog** — Activity Logging
- **Laravel Sanctum** — API Authentication
- **Laravel Cashier** — Stripe Integration
- **Tailwind CSS** — UI Styling
- **Alpine.js** — Frontend Interactions
- **MySQL** — Database

---

## ⚙️ Requirements

- PHP >= 8.2
- MySQL >= 8.0
- Composer >= 2.x
- Node.js >= 18

---

## 🚀 Quick Installation

```bash
# Step 1: Clone karo
git clone https://github.com/yourusername/saas-boilerplate.git
cd saas-boilerplate

# Step 2: Dependencies install karo
composer install

# Step 3: Environment setup
cp .env.example .env

# Step 4: One-command setup
php artisan saas:install

# Step 5: Server chalao
php artisan serve
```

---

## 🌐 URLs

| URL | Description |
|-----|-------------|
| `http://127.0.0.1:8000` | Landing page |
| `http://127.0.0.1:8000/register` | New workspace |
| `http://127.0.0.1:8000/login` | Admin login |
| `http://127.0.0.1:8000/admin/dashboard` | Admin panel |
| `http://{tenant}.localhost:8000/login` | Tenant login |
| `http://{tenant}.localhost:8000/dashboard` | Tenant app |

---

## 💳 Payments Setup

### Stripe
```env
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
```

### Razorpay
```env
RAZORPAY_KEY=rzp_live_xxxxx
RAZORPAY_SECRET=xxxxx
```

---

## 🔌 API Usage

```bash
# Login
POST /api/auth/login
Body: { "email": "user@example.com", "password": "password" }
Response: { "token": "...", "user": {...}, "tenant": {...} }

# Get current user
GET /api/auth/me
Header: Authorization: Bearer {token}

# Get tenant info
GET /api/tenant
Header: Authorization: Bearer {token}

# Get users list
GET /api/users
Header: Authorization: Bearer {token}
```

---

## 📋 Subscription Plans

| Feature | Free | Basic ($19/mo) | Pro ($49/mo) |
|---------|------|----------------|--------------|
| Users | 1 | 5 | Unlimited |
| Storage | 100MB | 2GB | Unlimited |
| API Calls/month | 100 | 5,000 | Unlimited |
| Custom Domain | ❌ | ❌ | ✅ |
| Priority Support | ❌ | ❌ | ✅ |
| White Label | ❌ | ❌ | ✅ |
| Activity Logs | ❌ | ✅ | ✅ |
| Team Management | ❌ | ✅ | ✅ |

---

## 🗂️ Project Structure

```
saas-boilerplate/
├── app/
│   ├── Console/Commands/SaasInstall.php    ← Install wizard
│   ├── Http/Controllers/
│   │   ├── Admin/                           ← Super admin
│   │   ├── Api/                             ← REST API
│   │   ├── Auth/                            ← Authentication
│   │   └── Tenant/                          ← Tenant features
│   ├── Models/
│   │   ├── Admin.php                        ← Central admin
│   │   ├── Tenant.php                       ← Tenant model
│   │   ├── Plan.php                         ← Subscription plans
│   │   └── User.php                         ← Tenant users
│   ├── Services/PaymentService.php          ← Stripe + Razorpay
│   └── Providers/TenancyServiceProvider.php
├── database/
│   ├── migrations/
│   │   ├── central/                         ← Central DB migrations
│   │   └── tenant/                          ← Per-tenant migrations
│   └── seeders/
│       ├── PlanSeeder.php                   ← Free, Basic, Pro plans
│       ├── AdminSeeder.php                  ← Super admin
│       └── TenantDatabaseSeeder.php         ← Roles per tenant
├── resources/views/
│   ├── admin/                               ← Admin panel views
│   ├── auth/                                ← Login, register
│   ├── emails/                              ← Email templates
│   ├── layouts/                             ← App layouts
│   └── tenant/                              ← Tenant app views
└── routes/
    ├── web.php                              ← Central routes
    ├── tenant.php                           ← Tenant routes
    └── api.php                              ← API routes
```

---

## 📞 Support

- 📧 Email: your@email.com
- ⏱️ Response time: Under 24 hours
- 🔄 Updates: Free lifetime updates

---

## 📄 License

MIT License — Free to use in personal and commercial projects.
