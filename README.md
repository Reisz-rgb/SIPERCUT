## Requirements

Make sure your local environment has:

* PHP >= 8.4
* Composer
* Git
* MySQL

---

## Local Setup Instructions

### 1. Clone the repository

```bash
git clone <repository-url>
cd SIPERCUT
```

### 2. Install PHP dependencies

```bash
composer install
```

> Note: The `vendor/` directory is intentionally not committed. Dependencies are installed locally via Composer.

---

### 3. Environment configuration

Create a local environment file:

```bash
cp .env.example .env
```

Generate a unique application key:

```bash
php artisan key:generate
```

Each developer must generate their own `APP_KEY`. Never share or commit `.env` files.

---

### 4. Disable database usage (current stage)

This project currently runs **without any database**.

Ensure the following values exist in your `.env`:

```env
DB_CONNECTION=null
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

No database connection is required at this stage.

---

### 5. Run the application

```bash
php artisan serve
```

Open your browser at:

```
http://127.0.0.1:8000
```

---

## Git & Security Guidelines

### Ignored Files

The following files/directories are intentionally excluded from version control:

* `vendor/`
* `.env`
* `node_modules/`
* `storage/logs/*`

These files are environment-specific or generated automatically.

### Committed Files

* `composer.json`
* `composer.lock`
* `.env.example`

This ensures consistent dependency versions while keeping secrets secure.


