#!/bin/bash
# Disable HMR mode and produce a fresh production build.
# Use this BEFORE deploying / pushing to main.
#
# Usage:  bash scripts/dev-off.sh

set -euo pipefail

cd "$(dirname "$0")/.."

echo "→ removing public/hot"
rm -f public/hot

echo "→ building production assets (2-3 min)…"
npm run build

echo "→ clearing Laravel caches"
php artisan view:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true

echo "✓ Production mode restored. Laravel serves from public/build/"
