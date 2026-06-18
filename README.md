# Shortlink Manager

سرویس مدیریت لینک کوتاه با PHP — بدون نیاز به Composer روی cPanel. از SQLite یا MySQL پشتیبانی می‌کند.

A lightweight PHP shortlink service with an admin panel, click analytics, and a 10-second redirect interstitial page. Supports SQLite or MySQL.

## Features

- Create short links from the admin panel (auto or custom slug)
- 10-second redirect page with VPN notice (Persian UI, RTL)
- Click tracking and per-link analytics charts
- SQLite (zero setup) or MySQL
- PHP 7.4+ compatible
- Works on cPanel with `mod_rewrite` (SQLite also needs writable `storage/`)

## Requirements

- PHP 7.4 or higher
- PDO extension with **SQLite** and/or **MySQL** driver
- Apache `mod_rewrite` (default on cPanel)

## Installation (cPanel)

1. Upload files to your hosting (or `git clone`).
2. Set **Document Root** to the `public/` folder.
   - Alternatively, point Document Root to the project root — the root `.htaccess` forwards requests to `public/`.
3. Copy config:
   ```bash
   cp app/config.example.php app/config.php
   ```
4. Edit `app/config.php`:
   - Choose database driver: `db_driver` → `sqlite` or `mysql`
   - Change `ip_salt` to a random string
   - Set `admin_password_hash` (see below)
   - Optionally set `base_url` (e.g. `https://yourdomain.com`)
5. **SQLite:** Ensure `storage/` is writable (`chmod 755 storage`). Tables are created automatically.
6. **MySQL:** Create an empty database in cPanel, then set `db_host`, `db_name`, `db_user`, `db_pass` in config. Tables are created automatically on first request.
7. Visit your site.

## Default login

After copying `config.example.php`, the default password is:

```
password
```

**Change this immediately in production.**

Generate a new hash:

```bash
php -r "echo password_hash('your-secure-password', PASSWORD_DEFAULT);"
```

Paste the output into `admin_password_hash` in `app/config.php`.

## URL structure

| URL | Description |
|-----|-------------|
| `/` | Redirects to `home_url` from config (or admin login if empty) |
| `/admin/login` | Admin login |
| `/admin/dashboard` | Dashboard with stats |
| `/admin/links` | Manage links |
| `/admin/links/{id}/stats` | Per-link analytics |
| `/{slug}` | Public short link redirect |

## Local development

```bash
php -S localhost:8000 -t public
```

Open http://localhost:8000/admin/login

## Configuration

| Key | Description |
|-----|-------------|
| `base_url` | Full site URL without trailing slash (auto-detected if empty) |
| `base_path` | Subdirectory path, e.g. `go` for `example.com/go` (auto-detected if empty) |
| `home_url` | Homepage redirect URL (empty = redirect to admin login) |
| `db_driver` | `sqlite` (default) or `mysql` |
| `db_path` | Path to SQLite file (when using sqlite) |
| `db_host` | MySQL host (when using mysql) |
| `db_port` | MySQL port (default: 3306) |
| `db_name` | MySQL database name |
| `db_user` | MySQL username |
| `db_pass` | MySQL password |
| `db_charset` | MySQL charset (default: utf8mb4) |
| `admin_password_hash` | Bcrypt hash for admin password |
| `ip_salt` | Salt for hashing visitor IPs |
| `redirect_delay` | Seconds before redirect (default: 10) |
| `login_max_attempts` | Failed logins before lockout |
| `login_lockout_seconds` | Lockout duration |

### SQLite (default)

```php
'db_driver' => 'sqlite',
'db_path' => __DIR__ . '/../storage/database.sqlite',
```

### MySQL (cPanel)

In cPanel → MySQL Databases, create a database and user, then:

```php
'db_driver' => 'mysql',
'db_host' => 'localhost',
'db_port' => 3306,
'db_name' => 'cpanel_user_shortlink',
'db_user' => 'cpanel_user_shortlink',
'db_pass' => 'your-database-password',
'db_charset' => 'utf8mb4',
```

## Subdirectory deployment (path خاص)

بله — می‌توانی پروژه را داخل یک مسیر خاص اجرا کنی، مثلاً:

```
https://example.com/go/admin/login
https://example.com/go/abc123
```

### روش ۱: زیرپوشه در دامنه اصلی (رایج‌ترین)

1. کل پروژه را داخل `public_html/go/` آپلود کن (ساختار کامل: `app/`, `public/`, `storage/`, `.htaccess`)
2. فایل `.htaccess` ریشه پروژه درخواست‌ها را به `public/` هدایت می‌کند
3. در `app/config.php` تنظیم کن:

```php
'base_url' => 'https://example.com/go',
// یا فقط:
'base_path' => 'go',
```

4. اگر لینک‌ها یا CSS درست لود نشد، `base_url` را کامل و دستی بگذار

### روش ۲: ساب‌دامین اختصاصی (پیشنهادی برای shortlink)

1. در cPanel → **Subdomains** یک ساب‌دامین بساز، مثلاً `links.example.com`
2. **Document Root** را روی پوشه `public/` پروژه بگذار:
   ```
   /home/username/shortlink/public
   ```
3. آدرس‌ها مستقیم می‌شوند:
   ```
   https://links.example.com/admin/login
   https://links.example.com/abc123
   ```
4. نیازی به `base_path` نیست

### روش ۳: دامنه جدا (Addon Domain)

مثل ساب‌دامین، Document Root را روی `public/` بگذار.

---

## راهنمای نصب cPanel (گام‌به‌گام)

### مرحله ۱: آپلود فایل‌ها

**File Manager** یا FTP:

```
/home/username/shortlink/     ← پیشنهادی (خارج از public_html)
  ├── app/
  ├── public/                 ← Document Root
  ├── storage/
  └── ...

یا:

/home/username/public_html/go/
  ├── app/
  ├── public/
  ├── storage/
  ├── .htaccess
  └── ...
```

### مرحله ۲: Document Root

| سناریو | Document Root |
|--------|---------------|
| ساب‌دامین / دامنه اختصاصی | `.../shortlink/public` |
| زیرپوشه `example.com/go` | `public_html/go` (کل پروژه اینجا) |

در cPanel: **Domains** → دامنه/ساب‌دامین → **Document Root** → Edit

### مرحله ۳: تنظیم config

```bash
cp app/config.example.php app/config.php
```

در `app/config.php`:

```php
// رمز ادمین — حتماً عوض کن
'admin_password_hash' => '...',

// برای زیرپوشه
'base_url' => 'https://yourdomain.com/go',

// دیتابیس — یکی را انتخاب کن:

// SQLite (ساده‌تر)
'db_driver' => 'sqlite',

// MySQL (پایدارتر برای هاست اشتراکی)
'db_driver' => 'mysql',
'db_host' => 'localhost',
'db_name' => 'username_shortlink',
'db_user' => 'username_shortlink',
'db_pass' => 'your-db-password',
```

### مرحله ۴: دیتابیس MySQL (اختیاری)

1. cPanel → **MySQL Databases**
2. Database جدید بساز: `username_shortlink`
3. User جدید بساز و به دیتابیس **ALL PRIVILEGES** بده
4. مقادیر را در `config.php` وارد کن
5. جداول خودکار ساخته می‌شوند — نیازی به import نیست

### مرحله ۵: دسترسی‌ها

```bash
chmod 755 storage
chmod 644 app/config.php
```

برای SQLite، پوشه `storage/` باید **writable** باشد.

### مرحله ۶: PHP Version

cPanel → **Select PHP Version** (یا MultiPHP Manager):

- PHP **7.4** یا بالاتر
- فعال بودن extensionهای: `pdo`, `pdo_sqlite` (یا `pdo_mysql`)

### مرحله ۷: تست

| آدرس | انتظار |
|------|--------|
| `/admin/login` | صفحه ورود |
| ورود با رمز | داشبورد |
| ساخت لینک | لینک کوتاه |
| `/{slug}` | صفحه انتقال ۱۰ ثانیه‌ای |

**نیازی به Cron، Composer، یا npm نیست.**

## Troubleshooting

**404 on all pages:** Enable `mod_rewrite` and ensure `.htaccess` is allowed (`AllowOverride All`).

**Database error (SQLite):** Check that `storage/` exists and is writable by the web server user.

**Database error (MySQL):** Verify `db_host`, `db_name`, `db_user`, and `db_pass`. On cPanel, the host is usually `localhost`. Ensure the MySQL user has full privileges on the database.

**CSS/JS not loading:** Confirm Document Root points to `public/` or that static files are not blocked. If using a subdirectory, set `base_url` or `base_path` in config.

**Wrong links in subdirectory:** Set `base_url` to the full URL including path, e.g. `https://example.com/go`

## Security notes

- Never commit `app/config.php` (it is in `.gitignore`)
- Use a strong admin password
- Change `ip_salt` in production
- Keep `storage/` and `app/` outside the web root when possible (Document Root = `public/`)

## License

MIT
