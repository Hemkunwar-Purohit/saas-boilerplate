# Laravel Multi-Tenant SaaS Boilerplate — Documentation

## Quick Start

```bash
# 1. Clone & install
composer install
cp .env.example .env
php artisan key:generate

# 2. One-command setup
php artisan saas:install

# 3. Serve
php artisan serve
```

## Architecture

```
Central DB (saas_central):
├── admins          — Super admin accounts
├── tenants         — All tenant workspaces
├── domains         — Subdomain → tenant mapping
├── plans           — Free, Basic, Pro
└── sessions        — Central sessions

Tenant DB (saas_tenant_{id}):
├── users           — Tenant's team members
├── roles           — owner, admin, member
├── permissions     — granular permissions
└── activity_log    — audit trail
```

## URLs

| URL | Description |
|-----|-------------|
| `http://127.0.0.1:8000` | Landing page |
| `http://127.0.0.1:8000/register` | New workspace registration |
| `http://127.0.0.1:8000/login` | Super admin login |
| `http://127.0.0.1:8000/admin/dashboard` | Admin panel |
| `http://{tenant}.localhost:8000/login` | Tenant login |
| `http://{tenant}.localhost:8000/dashboard` | Tenant dashboard |

## API Usage

```bash
# Login
curl -X POST http://tenant.localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get current user (with Bearer token)
curl http://tenant.localhost:8000/api/auth/me \
  -H "Authorization: Bearer {token}"

# Get tenant info
curl http://tenant.localhost:8000/api/tenant \
  -H "Authorization: Bearer {token}"
```

## Plans & Features

| Feature | Free | Basic | Pro |
|---------|------|-------|-----|
| Users | 1 | 5 | Unlimited |
| Storage | 100MB | 2GB | Unlimited |
| API Calls/month | 100 | 5,000 | Unlimited |
| Custom Domain | ❌ | ❌ | ✅ |
| Priority Support | ❌ | ❌ | ✅ |
| White Label | ❌ | ❌ | ✅ |

## Payments Setup

### Stripe
1. Create account at stripe.com
2. Add `STRIPE_KEY` and `STRIPE_SECRET` to `.env`
3. Create products/prices in Stripe dashboard
4. Update `stripe_monthly_price_id` and `stripe_yearly_price_id` in plans table

### Razorpay
1. Create account at razorpay.com
2. Add `RAZORPAY_KEY` and `RAZORPAY_SECRET` to `.env`
3. Install package: `composer require razorpay/razorpay`

## Tech Stack

- **Laravel 11** — PHP framework
- **stancl/tenancy v3.10** — Multi-tenancy
- **spatie/laravel-permission** — Roles & permissions
- **spatie/laravel-activitylog** — Activity logging
- **Laravel Sanctum** — API authentication
- **Laravel Cashier** — Stripe integration
- **Tailwind CSS** — UI styling
- **Alpine.js** — Frontend interactions

## Support

For issues, check the GitHub repository or contact support.
