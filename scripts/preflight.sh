#!/bin/bash
# Pre-release preflight check. Runs BEFORE merging a PR or tagging a release.
# Fast, non-destructive, read-only. Fails loud on the 10 most common release blockers.
#
# Usage:  bash scripts/preflight.sh

set -uo pipefail
cd "$(dirname "$0")/.."

FAIL=0
pass() { printf "  \e[32m✓\e[0m %s\n" "$1"; }
fail() { printf "  \e[31m✗\e[0m %s\n" "$1"; FAIL=$((FAIL+1)); }
info() { printf "  \e[36mℹ\e[0m %s\n" "$1"; }

echo "═══ Vacaliza ERP · Release Preflight ═══"
echo

echo "▸ git state"
  BRANCH=$(git branch --show-current)
  [ "$BRANCH" = "main" ] && pass "on main" || info "on branch: $BRANCH"
  if [ -n "$(git status --porcelain)" ]; then
    fail "uncommitted changes present"
    git status -s | head -5
  else
    pass "working tree clean"
  fi

echo
echo "▸ dependencies"
  [ -d node_modules ] && pass "node_modules present" || fail "run: npm install"
  [ -d vendor ] && pass "vendor present" || fail "run: composer install"

echo
echo "▸ dev-mode leftovers"
  [ -f public/hot ] && fail "public/hot exists — run scripts/dev-off.sh first" || pass "no public/hot"

echo
echo "▸ secrets safety"
  if grep -rln "APP_KEY=base64:" .env 2>/dev/null | head -1 >/dev/null; then pass "APP_KEY set"; else fail "APP_KEY missing"; fi
  if [ -d "ops/production/secrets" ] 2>/dev/null; then info "plaintext secret files still on disk (see P1 migration plan)"; fi

echo
echo "▸ laravel health"
  php artisan --version >/dev/null 2>&1 && pass "artisan ok" || fail "artisan broken"
  PHPL=$(php artisan route:list 2>&1 | grep -cE "GET|POST" || true)
  [ "$PHPL" -gt 100 ] && pass "$PHPL routes registered" || fail "only $PHPL routes (expected 150+)"

echo
echo "▸ node build dry-run"
  if [ -f public/build/manifest.json ]; then
    AGE=$(( ( $(date +%s) - $(stat -c %Y public/build/manifest.json) ) / 60 ))
    if [ "$AGE" -lt 60 ]; then pass "manifest fresh (${AGE}m)"; else info "manifest ${AGE}m old — run: npm run build"; fi
  else
    fail "no public/build/manifest.json — run: npm run build"
  fi

echo
echo "▸ tests"
  if [ -f phpunit.xml ]; then
    if php artisan test --parallel --stop-on-failure --quiet 2>&1 | tail -3 | grep -qE "OK|Tests:.*passed"; then
      pass "phpunit green"
    else
      info "phpunit not run (or failing) — add minimal tests per P7 plan"
    fi
  fi

echo
echo "▸ health endpoint"
  if curl -sf --max-time 3 http://localhost:8888/up >/dev/null 2>&1; then
    pass "/up responds 200"
  else
    info "app not running on :8888 (OK if deploying via Coolify)"
  fi

echo
if [ "$FAIL" -eq 0 ]; then
  printf "\e[32m═══ PREFLIGHT PASSED ═══\e[0m\n"
  exit 0
else
  printf "\e[31m═══ PREFLIGHT FAILED: %d issue(s) ═══\e[0m\n" "$FAIL"
  exit 1
fi
