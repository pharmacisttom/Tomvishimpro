#!/usr/bin/env bash

set -e

echo "========================================================"
echo " TomvisHIMPro Zero-Reconfiguration Linux Deploy Script"
echo "========================================================"

# 1. Preflight
echo "[1/4] Running Preflight Verification..."
php scripts/preflight.php

# 2. Dependencies
echo "[2/4] Installing Production Dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Database Migration
echo "[3/4] Running Database Migrations..."
php scripts/tomvis migrate

# 4. Post-Deploy Health Check
echo "[4/4] Running Post-Deployment Health Check..."
php scripts/post-deploy-check.php

echo "[SUCCESS] TomvisHIMPro Deployment Completed!"
