# Hakuna Matata v3 — POS & Delivery System

A PHP-based restaurant POS system with a REST API, customer ordering, kitchen display, driver tracking, and admin dashboard.

---

## 🚀 Deploy on Railway

### 1. Add a MySQL database
In Railway, click **New** → **Database** → **MySQL**. Railway will give you connection variables automatically.

### 2. Set Environment Variables
In your Railway service, go to **Variables** and add:

| Variable | Value |
|---|---|
| `DB_HOST` | From Railway MySQL (e.g. `mysql.railway.internal`) |
| `DB_NAME` | From Railway MySQL |
| `DB_USER` | From Railway MySQL |
| `DB_PASS` | From Railway MySQL |
| `JWT_SECRET` | A long random string (e.g. `MyApp@Secret#2026!`) |
| `APP_ENV` | `production` |
| `APP_URL` | Your Railway app URL (e.g. `https://yourapp.up.railway.app/api`) |
| `CORS_ORIGINS` | Your frontend URL (e.g. `https://yourapp.up.railway.app`) |

### 3. Import the Database
After deploying, connect to your Railway MySQL and import the schema:
```
hakunamatata_v3.sql
```
You can use [TablePlus](https://tableplus.com/) or the Railway MySQL shell.

### 4. Push to GitHub and connect to Railway
Railway auto-deploys from your GitHub repo.

---

## 📁 Project Structure

```
hakunamatata_v3/
├── api/                  # REST API
│   ├── config.php        # DB + JWT config (reads from env vars)
│   ├── core.php          # Bootstrap, DB, helpers
│   ├── index.php         # Router
│   ├── middleware/       # Auth middleware
│   ├── routes/           # API route handlers
│   └── uploads/          # Product images
├── customer/             # Customer-facing pages
├── admin.php             # Admin dashboard
├── kitchen.php           # Kitchen display
├── driver.php            # Driver app
├── pos.php               # Point of Sale
├── login.php / register.php
└── hakunamatata_v3.sql   # Database schema
```

---

## 🔧 Local Development

1. Run on XAMPP/Laragon
2. Import `hakunamatata_v3.sql` into MySQL
3. Edit `api/config.php` with your local DB credentials
4. Visit `http://localhost/hakunamatata_v3/`
