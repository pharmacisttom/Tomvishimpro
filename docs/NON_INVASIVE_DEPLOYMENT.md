# TomvisHIMPro 3.0 — Non-Invasive Deployment Policy & Architecture

## Core Non-Invasive Deployment Rules

### 1. Mandatory Prohibitions
Deployment scripts, CLI tools, and installation procedures are **STRICTLY FORBIDDEN** from invoking:
- `systemctl restart apache2` / `systemctl reload apache2`
- `service apache2 restart` / `service httpd restart`
- `apachectl graceful` / `apache2ctl reload`
- `a2enmod` / `a2dismod`
- Direct editing of global Apache configuration files (`/etc/apache2/apache2.conf`, `httpd.conf`, `000-default.conf`).
- Direct editing of global `php.ini`.
- Modifying parent directory `.htaccess` or adjacent hospital applications (`PDHTelemed`, `PDHTawan`).

### 2. Preflight Deployment Command Scanner
`scripts/preflight.php` automatically scans all deployment scripts (`deploy.sh`, `deploy.ps1`, `rollback.sh`) for forbidden service restart strings. If any prohibited command is found, **deployment is BLOCKED automatically**.

### 3. Subdirectory Entry Point (`/tomvishimpro/index.php`)
For shared hosting environments where DocumentRoot points to `d:/xampp/htdocs/tomvishimpro` or `/var/www/html/tomvishimpro` instead of `public/`, a non-invasive root entry point `d:/xampp/htdocs/tomvishimpro/index.php` is provided. It safely forwards requests to `public/index.php` or Front Controller Fallback without modifying Apache DocumentRoot.
