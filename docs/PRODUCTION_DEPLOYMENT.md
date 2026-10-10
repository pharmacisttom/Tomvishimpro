# TomvisHIMPro Hospital Production Deployment Guide

## Pre-Deployment Setup (One-time Server Configuration)

1. **Configure Production Environment `.env`:**
   Copy template `config/environments/.env.production.example` to `/var/www/tomvishimpro/.env`.
   Set production credentials on server. **Git will never track or overwrite `.env`**.

2. **Run Automated Deployment Pipeline:**
   - Linux Shell:
     ```bash
     bash scripts/deploy.sh
     ```
   - Windows PowerShell:
     ```powershell
     .\scripts\deploy.ps1
     ```
   - CLI Command:
     ```bash
     php scripts/tomvis deploy
     ```

3. **Pipeline Stages Handled Automatically:**
   - Step 1: Preflight Verification (`php scripts/preflight.php`).
   - Step 2: Optimized Composer Dependencies (`composer install --no-dev --optimize-autoloader`).
   - Step 3: Database Migrations (`php scripts/tomvis migrate`).
   - Step 4: Post-Deploy Health Check (`php scripts/post-deploy-check.php`).
