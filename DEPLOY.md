# SKYNITY WiFi — Coolify Deployment Guide

## প্রয়োজনীয় জিনিস

- Coolify installed server (VPS/Dedicated)
- GitHub বা GitLab account (code push করার জন্য)
- MySQL/MariaDB database (Coolify-তেই তৈরি করা যাবে)
- Domain name (optional, কিন্তু recommended)

---

## Step 1 — Code Push করুন

Local machine থেকে GitHub/GitLab-এ push করুন:

```bash
git add .
git commit -m "production ready"
git push origin master
```

---

## Step 2 — APP_KEY Generate করুন

> **এটি অবশ্যই আগে করতে হবে।** APP_KEY ছাড়া app কাজ করবে না।

**Option A — Local-এ PHP আছে:**
```bash
php artisan key:generate --show
```

**Option B — PHP নেই, Online generator:**
1. এই সাইটে যান: https://generate-secret.vercel.app/32
2. একটি random string পাবেন, যেমন: `a1b2c3d4e5f6...`
3. শুরুতে `base64:` লাগান: `base64:a1b2c3d4e5f6...`

Generated key টি কোথাও সেভ রাখুন — পরে Coolify-তে দেবেন।

---

## Step 3 — Coolify-তে MySQL Database তৈরি করুন

1. Coolify Dashboard → **Resources** → **+ New Resource**
2. **Database** → **MySQL** বা **MariaDB** select করুন
3. নিচের মতো fill করুন:

| Field | Value |
|-------|-------|
| Name | `skynity-db` |
| Database Name | `skynity` |
| Username | `skynity` |
| Password | (strong password দিন) |

4. **Save** করুন
5. Database তৈরি হলে **Internal URL** note করুন (যেমন: `skynity-db` বা `172.x.x.x`)

---

## Step 4 — Coolify-তে Application তৈরি করুন

1. Coolify Dashboard → **Resources** → **+ New Resource**
2. **Application** select করুন
3. Git provider select করুন (GitHub/GitLab)
4. Repository select করুন (skynity)
5. নিচের settings দিন:

| Setting | Value |
|---------|-------|
| Branch | `master` |
| Build Pack | `Dockerfile` |
| Port | `80` |
| Dockerfile Location | `/Dockerfile` |

6. **Save** করুন — এখনই Deploy করবেন না।

---

## Step 5 — Environment Variables সেট করুন

Application-এর **Environment Variables** tab-এ যান এবং নিচের সব variable যোগ করুন।

> সব variable একসাথে paste করতে পারবেন "Paste as .env" option দিয়ে।

```env
APP_NAME=SKYNITY-WiFi
APP_ENV=production
APP_KEY=base64:আপনার_generate_করা_key_এখানে
APP_DEBUG=false
APP_URL=https://আপনার-domain.com
APP_TIMEZONE=Asia/Dhaka

DB_CONNECTION=mysql
DB_HOST=আপনার-mysql-internal-hostname
DB_PORT=3306
DB_DATABASE=skynity
DB_USERNAME=skynity
DB_PASSWORD=আপনার-db-password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error

WIREGUARD_ENABLED=false
```

> **DB_HOST** — Coolify-এ MySQL service-এর internal hostname/IP দিন।
> Coolify Dashboard → আপনার MySQL service → Connection tab-এ পাবেন।

---

## Step 6 — Domain সেট করুন (Optional)

1. Application → **Domains** tab
2. আপনার domain যোগ করুন, যেমন: `https://skynity.org`
3. Coolify automatically SSL (Let's Encrypt) configure করবে

---

## Step 7 — Deploy করুন

1. Application page-এ **Deploy** button চাপুন
2. **Logs** tab-এ দেখুন — নিচের মতো output আসবে:

```
[0/6] APP_KEY is set.
[1/6] Waiting for database...
  Database is ready!
[2/6] Storage link...
[3/6] Running migrations...
[4/6] Caching...
[5/6] Setting permissions...
[6/6] Starting services...
```

3. সব `[x/6]` complete হলে app ready।

---

## Step 8 — Admin Account তৈরি করুন

Deploy হওয়ার পর একবার Coolify terminal বা SSH দিয়ে run করুন:

```bash
# Coolify → Application → Terminal tab
php artisan tinker

# Tinker-এ এটি run করুন:
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('your-strong-password'),
    'role' => 'admin',
]);
```

তারপর `https://আপনার-domain.com/login` এ login করুন।

---

## সমস্যা সমাধান

### App খুলছে না / 500 Error

```bash
# Coolify Terminal-এ run করুন:
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Database Connection Error

- `DB_HOST` ঠিক আছে কিনা check করুন
- Coolify-এ MySQL এবং App একই **Network**-এ আছে কিনা দেখুন
- MySQL service running আছে কিনা check করুন

### Migration Failed

```bash
# Coolify Terminal-এ:
php artisan migrate:status
php artisan migrate --force
```

### APP_KEY Error / Encryption Error

```bash
# Coolify Terminal-এ:
php artisan key:generate --force
php artisan config:clear
```

---

## Deploy পরবর্তী Coolify Auto-Deploy Setup

Code push করলে automatically deploy হওয়ার জন্য:

1. Application → **General** tab
2. **Auto Deploy** → Enable করুন
3. GitHub/GitLab-এ Webhook automatically যোগ হবে

এরপর `git push` করলেই Coolify নিজে থেকে নতুন version deploy করবে।

---

## File Structure (Dockerfile ব্যবহার করে)

```
skynity/
├── Dockerfile              ← Coolify এটি দিয়ে build করে
├── .dockerignore           ← .env সহ sensitive files exclude
├── docker/
│   ├── nginx.conf          ← Nginx config
│   ├── php.ini             ← PHP production config
│   ├── supervisord.conf    ← Process manager (nginx + php-fpm + queue)
│   └── start.sh            ← Container startup script
├── app/
├── routes/
├── database/
└── ...
```

---

> **Support:** কোনো সমস্যা হলে Coolify-এর deployment log দেখুন।
> Log-এ error message copy করে সাহায্য নিন।
