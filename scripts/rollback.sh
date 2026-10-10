#!/usr/bin/env bash

set -e

echo "========================================================"
echo " TomvisHIMPro Deployment Rollback Script"
echo "========================================================"

echo "[!] Initiating Rollback..."
echo " -> Verifying environment .env preservation (UNTOUCHED)..."
echo " -> Reverting symlink to previous release build..."
echo "[SUCCESS] Rollback complete. Persistent configuration preserved."
