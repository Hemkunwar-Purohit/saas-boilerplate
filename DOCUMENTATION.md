# 📚 Laravel Multi-Tenant SaaS Boilerplate — Complete Documentation

---

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Architecture](#architecture)
4. [Multi-Tenancy](#multi-tenancy)
5. [Authentication](#authentication)
6. [Roles & Permissions](#roles--permissions)
7. [Payments (Stripe)](#payments-stripe)
8. [Payments (Razorpay)](#payments-razorpay)
9. [REST API](#rest-api)
10. [Email Notifications](#email-notifications)
11. [Deployment](#deployment)
12. [Troubleshooting](#troubleshooting)

---

## 1. Installation

### Requirements
- PHP >= 8.2
- MySQL >= 8.0
- Composer >= 2.x
- Node.js >= 18

### Step-by-step

```bash
# 1. Files extract karo
unzip saas-boilerplate.zip
cd saas-boilerplate

# 2. Dependencies install karo
composer install
npm install

# 3. Environment file setup
cp .env.example .env

# 4. .env mein database config karo (Step 2 dekho)

# 5. One-command install
php artisan saas:install
# Yeh automatically:
# - Database migrate karega
# - Plans seed karega (Free, Basic, Pro)
# - Super admin account banayega
# - Storage link banayega

# 6. Server start karo
php artisan serve
```

---

## 2. Configuration

### .env file setup

```env
# App
APP_NAME="Your SaaS Name"
APP_URL=http://127.0.0.1:8000
APP_DOMAIN=127.0.0.1          # Central domain

# Database (Central)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=saas_central      # Pehle MySQL mein banao
DB_USERNAME=root
DB_PASSWORD=your_password

# Tenant DB prefix
TENANCY_DATABASE_PREFIX=saas_tenant_

# Session
SESSION_DRIVER=file
SESSION_DOMAIN=null

# Mail (Mailtrap for testing)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_user
MAIL_PASSWORD=your_mailtrap_pass
MAIL_FROM_ADDRESS="hello@yoursaas.com"

# Stripe
STRIPE_KEY=pk_test_xxxxx
STRIPE_SECRET=sk_test_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx

# Razorpay
RAZORPAY_KEY=rzp_test_xxxxx
RAZORPAY_SECRET=xxxxx
```

### MySQL mein database banao

```sql
CREATE DATABASE saas_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 3. Architecture

```
┌─────────────────────────────────────────────────┐
│                  CENTRAL DOMAIN                  │
│              127.0.0.1:8000                      │
│                                                  │
│  Landing → Register → Admin Panel               │
│                                                  │
│  saas_central database:                         │
│  ├── admins (super admin accounts)              │
│  ├── tenants (all workspaces)                   │
│  ├── domains (subdomain mapping)                │
│  ├── plans (Free, Basic, Pro)                   │
│  └── sessions                                   │
└─────────────────────────────────────────────────┘
                        │
            ┌───────────┴───────────┐
            │                       │
┌───────────▼──────┐    ┌──────────▼───────┐
│  TENANT A        │    │  TENANT B        │
│  acme.localhost  │    │  startup.localhost│
│                  │    │                  │
│  saas_tenant_    │    │  saas_tenant_    │
│  acme database:  │    │  startup DB:     │
│  ├── users       │    │  ├── users       │
│  ├── roles       │    │  ├── roles       │
│  └── activity    │    │  └── activity    │
└──────────────────┘    └──────────────────┘
```

### How multi-tenancy works

1. User `acme.yourdomain.com` pe visit karta hai
2. `InitializeTenancyByDomainEarly` middleware domain check karta hai
3. `acme` domain se tenant identify hota hai
4. Database automatically `saas_tenant_acme` pe switch ho jaata hai
5. Saare queries ab `saas_tenant_acme` mein hoti hain
6. Data completely isolated hai — Tenant A ka data Tenant B ko kabhi nahi dikhega

---

## 4. Multi-Tenancy

### Naya tenant kaise banta hai

```
User /register pe form fill karta hai
→ RegisterController tenant create karta hai
→ stancl/tenancy automatically:
   ├── New database banata hai (saas_tenant_xyz)
   ├── Migrations run karta hai (users, roles, permissions)
   ├── TenantDatabaseSeeder run karta hai (roles seed)
   └── Domain register karta hai (xyz.yourdomain.com)
→ Owner user tenant DB mein create hota hai
→ Owner role assign hoti hai
→ Tenant ke subdomain pe redirect
```

### Tenant context mein central data access karna

```php
// Tenant context mein ho aur central DB se plans chahiye:
$plans = Plan::on('mysql')->get();

// Tenant context mein central tenant model:
$centralTenant = \App\Models\Tenant::find(tenant()->id);
```

---

## 5. Authentication

### Two separate auth systems

| System | Guard | Model | Database |
|--------|-------|-------|----------|
| Super Admin | `admin` | `App\Models\Admin` | `saas_central.admins` |
| Tenant User | `web` | `App\Models\User` | `saas_tenant_xxx.users` |

### Super Admin banao

```bash
php artisan db:seed --class=AdminSeeder
# Email: admin@saasapp.com
# Password: password123

# Ya custom:
php artisan tinker
>>> App\Models\Admin::create(['name'=>'Admin','email'=>'you@example.com','password'=>bcrypt('yourpassword')]);
```

### Tenant User roles

| Role | Permissions |
|------|-------------|
| **owner** | Sab kuch — complete access |
| **admin** | Users manage, settings, reports (owner delete nahi kar sakta) |
| **member** | Sirf view access |

---

## 6. Roles & Permissions

### Default permissions

```
view users, create users, edit users, delete users, invite users
view billing, manage billing
view settings, manage settings
view reports
manage api tokens
view activity logs
```

### Custom permission add karna

```php
// TenantDatabaseSeeder.php mein add karo:
Permission::findOrCreate('your-new-permission', 'web');
$adminRole->givePermissionTo('your-new-permission');
```

### Controller mein permission check

```php
// Middleware se
Route::middleware('permission:view billing')->group(function () { ... });

// Controller mein
if (!auth()->user()->can('manage billing')) {
    abort(403);
}

// Blade mein
@can('view reports')
    <a href="/reports">Reports</a>
@endcan
```

---

## 7. Payments (Stripe)

### Setup

1. `stripe.com` pe account banao
2. Dashboard → Developers → API Keys
3. `.env` mein keys daalo
4. Products create karo Stripe dashboard mein
5. Price IDs copy karo `plans` table mein

```sql
UPDATE plans SET 
    stripe_monthly_price_id = 'price_xxxxx',
    stripe_yearly_price_id = 'price_yyyyy'
WHERE slug = 'basic';
```

### Webhook setup

```bash
# Local testing ke liye:
stripe listen --forward-to localhost:8000/stripe/webhook

# .env mein:
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
```

---

## 8. Payments (Razorpay)

### Setup

1. `razorpay.com` pe account banao
2. Settings → API Keys
3. `.env` mein keys daalo

```bash
# Package install karo (agar nahi hai):
composer require razorpay/razorpay
```

### Indian market ke liye

Razorpay INR mein charge karta hai. Plan prices accordingly set karo:

```sql
UPDATE plans SET price_monthly = 999 WHERE slug = 'basic';   -- ₹999/month
UPDATE plans SET price_monthly = 2999 WHERE slug = 'pro';    -- ₹2999/month
```

---

## 9. REST API

### Base URL
```
http://{tenant}.yourdomain.com/api/
```

### Endpoints

#### Authentication

```bash
# Login
POST /api/auth/login
Content-Type: application/json
{
    "email": "user@example.com",
    "password": "password123"
}

# Response:
{
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        "roles": ["owner"]
    },
    "tenant": {
        "id": "acme",
        "name": "Acme Corp",
        "plan": "Pro"
    }
}

# Logout
POST /api/auth/logout
Authorization: Bearer {token}

# Get current user
GET /api/auth/me
Authorization: Bearer {token}
```

#### Tenant

```bash
# Tenant info
GET /api/tenant
Authorization: Bearer {token}

# Response:
{
    "id": "acme",
    "name": "Acme Corp",
    "plan": {
        "name": "Pro",
        "price_monthly": 49,
        "features": {...}
    },
    "is_active": true,
    "on_trial": false
}
```

#### Users

```bash
# Users list
GET /api/users
Authorization: Bearer {token}

GET /api/users?search=john   # Search
```

---

## 10. Email Notifications

### Mail setup

```env
# Testing (Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxx
MAIL_PASSWORD=xxxxx

# Production (Gmail example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_app_password
```

### Welcome email manually send karo

```php
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;

Mail::to($user->email)->send(new WelcomeMail(
    $user,
    tenant()->name,
    tenant()->id . '.yourdomain.com'
));
```

---

## 11. Deployment

### Production pe deploy karna

```bash
# 1. Dependencies
composer install --no-dev --optimize-autoloader

# 2. Environment
cp .env.example .env
# .env configure karo

# 3. Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Setup
php artisan tenancy:install
php artisan migrate --path=database/migrations/central --force
php artisan db:seed --class=PlanSeeder --force
php artisan db:seed --class=AdminSeeder --force
php artisan storage:link
```

### Subdomain setup (cPanel)

1. cPanel → Subdomains
2. Wildcard subdomain banao: `*.yourdomain.com`
3. Document root: `/public_html/saas-boilerplate/public`
4. `.env` mein update karo:

```env
APP_URL=https://yourdomain.com
APP_DOMAIN=yourdomain.com
CENTRAL_DOMAIN=yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

---

## 12. Troubleshooting

### "Table saas_central.users does not exist"
```
Problem: Tenant context mein central table access ho rahi hai
Fix: Ensure tenancy is properly initialized before auth
Check: InitializeTenancyByDomainEarly middleware registered hai ya nahi
```

### "Tenant could not be identified on domain"
```
Problem: Domain central_domains list mein nahi hai
Fix: config/tenancy.php mein central_domains check karo
     '127.0.0.1' aur 'localhost' hona chahiye
```

### "Table saas_tenant_xxx.plans does not exist"
```
Problem: Plans central DB mein hain, tenant mein nahi
Fix: Plan::on('mysql')->get() use karo tenant context mein
```

### Migration error "Table already exists"
```
Fix: php artisan migrate:fresh (development mein only!)
     Ya specific table drop karo MySQL mein
```

### Session conflict (login hone pe doosra logout)
```
Problem: Same cookie name dono domains pe
Fix: AppServiceProvider mein session cookie logic check karo
     .env mein SESSION_DRIVER=file hona chahiye
```

---

## 📞 Support

Questions? Comments section mein poochho ya email karo.

**Response time:** Under 24 hours

**Free updates:** Yes, lifetime

---

*Made with ❤️ using Laravel 11*
