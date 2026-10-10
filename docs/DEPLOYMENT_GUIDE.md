# TomvisHIMPro Hospital Production Deployment Guide

## Production Environment Prerequisites
- OS: Ubuntu 22.04 LTS / 24.04 LTS (Hospital Approved OS)
- Web Server: Nginx with PHP-FPM 8.2+ or Apache 2.4+
- Database: MySQL 8.0+ Primary Server with SSL enabled
- Network: Isolated Hospital VLAN with Read-Only HIS Gateway access

## Nginx Virtual Host Configuration Example

```nginx
server {
    listen 80;
    server_name himpro-framework.hospital.local;
    root /var/www/tomvishimpro/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\. {
        deny all;
    }
}
```

## Post-Deployment Security Checklist
- Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.
- Ensure document root points exclusively to `/public`. Protect `config/`, `storage/`, `vendor/`, and `.env` from direct web access.
- Execute database migrations via `php scripts/tomvis migrate`.
- Run `vendor\bin\phpunit` to confirm installation integrity.
