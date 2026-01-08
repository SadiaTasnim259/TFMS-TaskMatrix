# Task Force Management System (TFMS)

A comprehensive management system built with Laravel 12 and Tailwind CSS 4 for managing academic task forces, workload distribution, and performance reporting.

## 📋 Prerequisites & System Requirements

Before setting up the project, ensure your development environment meets the following requirements:

### Operating System
- **macOS**, **Linux**, or **Windows** (via WSL2 recommended)

### Required Software
- **PHP**: Version 8.2 or higher
  - Extensions: `pdo`, `sqlite` (or `mysql`/`pgsql`), `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `tokenizer`, `xml`
- **Composer**: Dependency manager for PHP (Latest version)
- **Node.js**: Version 20.x (LTS) or higher
- **NPM**: Package manager for Node.js
- **Git**: Version control system

### Database
- **SQLite** (Recommended for local development, usually pre-installed on macOS/Linux)
- *Optional:* MySQL 8.0+ or PostgreSQL 14+ if preferred.

### 🍎 Installing Requirements on macOS
The easiest way to install these tools on macOS is using [Homebrew](https://brew.sh):

```bash
# Install PHP, Composer, and Node.js
brew install php composer node

# (Optional) Install MySQL if not using SQLite
brew install mysql
```

#### Detailed MySQL Setup on macOS

**1. Check if MySQL is Installed**
```bash
mysql --version
```
If you see `command not found`, install it via Homebrew.

**2. Install and Start MySQL**
```bash
brew install mysql
brew services start mysql
```

**3. Configure the Database**
By default, the `root` user has no password.
```bash
mysql -u root
```
Inside the MySQL shell, create the database:
```sql
CREATE DATABASE tfms;
EXIT;
```

**4. Connect Project to Database**
Update your `.env` file:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tfms
DB_USERNAME=root
DB_PASSWORD=
```
Then run migrations: `php artisan migrate`.

---

## 🛠️ Project Setup Instructions

Follow these steps to set up the project on a fresh machine.

### 1. Clone the Repository
```bash
git clone <repository-url>
cd tfms
```

### 2. Install Backend Dependencies
Install the required PHP packages using Composer:
```bash
composer install
```

### 3. Configure Environment Variables
Copy the example environment file to create your local configuration:
```bash
cp .env.example .env
```

Generate the application encryption key:
```bash
php artisan key:generate
```

### 4. Database Setup

#### Option A: SQLite (Simplest for Development)
1. Ensure the database driver is set to `sqlite` in your `.env` file (this is the default):
   ```ini
   DB_CONNECTION=sqlite
   # DB_HOST=... (comment out or remove other DB_ settings)
   ```
2. Create the SQLite database file:
   ```bash
   touch database/database.sqlite
   # On Windows (cmd): type nul > database\database.sqlite
   ```

#### Option B: MySQL / PostgreSQL
1. Create a new database in your database engine (e.g., `create database tfms;`).
2. Update your `.env` file with your credentials:
   ```ini
   DB_CONNECTION=mysql  # or pgsql
   DB_HOST=127.0.0.1
   DB_PORT=3306         # or 5432
   DB_DATABASE=tfms
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

### 5. Run Migrations & Seeders
Create the database tables and populate them with initial test data (roles, users, configurations):
```bash
php artisan migrate --seed
```

### 6. Install Frontend Dependencies
Install the JavaScript libraries:
```bash
npm install
```

Build the frontend assets:
```bash
npm run build
```

---

## 🚀 Running the Project

### Development Server
The project includes a convenient command to run all necessary services (Laravel Server, Queue Worker, Vite) simultaneously:

```bash
composer run dev
```

This will start:
- **Laravel Server**: `http://localhost:8000`
- **Vite (Frontend)**: Hot Module Replacement (HMR) for instant CSS/JS updates.
- **Queue Listener**: Processes background jobs.

You should see output indicating all services are running. Open your browser to **http://localhost:8000**.

### Default Login Credentials (from Seeders)
If you ran `--seed`, the following default users are available:

- **Admin**: `admin@example.com` / `password`
- **HOD**: `hod@example.com` / `password`
- **PSM**: `psm@example.com` / `password`
- **Lecturer**: `user@example.com` / `password`

---

## 🐳 Docker Setup (Alternative)

If you prefer using Docker, a `Dockerfile` is provided.

1. **Build the image:**
   ```bash
   docker build -t tfms-app .
   ```

2. **Run the container:**
   ```bash
   docker run -p 8000:7860 tfms-app
   ```
   *Note: Using the provided Dockerfile requires external DB configuration as it sets up a production-ready image. For a local dev environment with Docker, using Laravel Sail is recommended instead.*

---

## ❓ Troubleshooting

### Common Issues

**1. "Database file does not exist" (SQLite)**
- **Cause:** You didn't create the `database.sqlite` file.
- **Fix:** Run `touch database/database.sqlite`.

**2. "Permission denied" for `storage` or `bootstrap/cache`**
- **Cause:** Web server/script needs write permissions.
- **Fix:**
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

**3. "Vite manifest not found"**
- **Cause:** You tried to access the site without building frontend assets.
- **Fix:** Run `npm run build` (for production) or ensure `npm run dev` is running (for development).

**4. "500 Server Error"**
- **Check Logs:** Look at `storage/logs/laravel.log`.
- **Common Fix:** Ensure `.env` exists and `php artisan key:generate` was run.

### Setup Verification
To confirm everything is installed correctly:
```bash
php artisan about
```
This command will display the environment details, including PHP version, Laravel version, and Cache status.
