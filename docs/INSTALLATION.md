# TomvisHIMPro Installation & Setup Guide

## System Requirements
- **OS:** Windows 10/11 (XAMPP Development) / Ubuntu 22.04 LTS (Production Server)
- **PHP:** 8.2, 8.3, or 8.4 with extensions: `pdo`, `pdo_mysql`, `mbstring`, `json`, `ctype`, `xml`
- **Database:** MySQL 8.0+ / MariaDB 10.4+
- **Web Server:** Apache 2.4+ (mod_rewrite enabled) or Nginx
- **Tools:** Composer 2.x, Git

---

## Installation Steps (Windows XAMPP)

1. **Extract / Clone Source Code:**
   Navigate to XAMPP web root `d:\xampp\htdocs\tomvishimpro`

2. **Composer Installation:**
   Run:
   ```cmd
   composer install
   ```

3. **Environment Setup:**
   Copy `.env.example` to `.env`:
   ```cmd
   copy .env.example .env
   ```
   Verify database configuration in `.env`:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tomvishimpro
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Initialize Database & Seed Data:**
   Run the CLI tool:
   ```cmd
   php scripts/tomvis migrate
   php scripts/tomvis seed
   ```

5. **Run Automated Test Suite:**
   Verify implementation:
   ```cmd
   vendor\bin\phpunit
   ```

6. **Web Access:**
   Open browser at `http://localhost/tomvishimpro/public/`
   Login with `admin` / `Admin123456!`.
