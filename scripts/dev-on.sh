#!/bin/bash
# Enable Vite HMR dev mode: Laravel serves assets from the Vite dev server
# instead of public/build/. Changes reload in the browser instantly.
#
# Usage:
#   bash scripts/dev-on.sh
#   npm run dev -- --host 0.0.0.0     # start Vite in a separate terminal
#
# Your app URL stays the same (http://HOST:8888). HMR uses :5173 behind the scenes.

set -eu

cd "$(dirname "$0")/.."

VITE_HOST="${VITE_HOST:-144.126.151.143}"
VITE_PORT="${VITE_PORT:-5173}"

echo "http://${VITE_HOST}:${VITE_PORT}" > public/hot
echo "✓ public/hot written → http://${VITE_HOST}:${VITE_PORT}"
echo "  Laravel now serves assets from the Vite dev server."
echo "  Start Vite if not running:  npm run dev -- --host 0.0.0.0"
echo "  Reload your browser once; subsequent edits hot-reload automatically."
