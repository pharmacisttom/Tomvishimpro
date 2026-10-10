# TomvisHIMPro Windows PowerShell Automated Deployment Script
param (
    [switch]$DryRun
)

Write-Host "========================================================" -ForegroundColor Cyan
Write-Host " TomvisHIMPro Zero-Reconfiguration PowerShell Deployment" -ForegroundColor Cyan
Write-Host "========================================================" -ForegroundColor Cyan

if ($DryRun) {
    Write-Host "[DRY RUN MODE] Simulating deployment steps without executing changes..." -ForegroundColor Yellow
}

# 1. Preflight
Write-Host "[1/4] Running Preflight Verification..." -ForegroundColor Green
php scripts/preflight.php
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Preflight failed. Aborting deployment." -ForegroundColor Red
    exit 1
}

# 2. Composer Install
Write-Host "[2/4] Installing Production Dependencies..." -ForegroundColor Green
if (-not $DryRun) {
    composer install --no-dev --optimize-autoloader
}

# 3. Database Migration
Write-Host "[3/4] Running Database Migrations..." -ForegroundColor Green
if (-not $DryRun) {
    php scripts/tomvis migrate
}

# 4. Post-Deploy Health Check
Write-Host "[4/4] Running Post-Deployment Health Check..." -ForegroundColor Green
php scripts/post-deploy-check.php

Write-Host "[SUCCESS] TomvisHIMPro Deployment Completed!" -ForegroundColor Green
