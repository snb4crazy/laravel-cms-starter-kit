#!/usr/bin/env bash

set -euo pipefail

usage() {
  cat <<'EOF'
Usage:
  scripts/switch-admin-panel.sh <filament|inertia> [--dry-run]

Examples:
  scripts/switch-admin-panel.sh filament
  scripts/switch-admin-panel.sh inertia --dry-run
EOF
}

if [[ ${1:-} == "-h" || ${1:-} == "--help" || $# -lt 1 ]]; then
  usage
  exit 0
fi

PANEL="$1"
DRY_RUN="false"

if [[ ${2:-} == "--dry-run" ]]; then
  DRY_RUN="true"
fi

if [[ "$PANEL" != "filament" && "$PANEL" != "inertia" ]]; then
  echo "Error: panel must be 'filament' or 'inertia'"
  usage
  exit 1
fi

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env"
ENV_EXAMPLE_FILE="$ROOT_DIR/.env.example"

if [[ ! -f "$ENV_FILE" ]]; then
  if [[ -f "$ENV_EXAMPLE_FILE" ]]; then
    cp "$ENV_EXAMPLE_FILE" "$ENV_FILE"
    echo "Created .env from .env.example"
  else
    echo "Error: neither .env nor .env.example exists"
    exit 1
  fi
fi

if grep -q '^ADMIN_PANEL=' "$ENV_FILE"; then
  if [[ "$DRY_RUN" == "true" ]]; then
    echo "[dry-run] Would update ADMIN_PANEL=$PANEL in .env"
  else
    awk -v value="$PANEL" 'BEGIN { changed=0 }
      /^ADMIN_PANEL=/ { print "ADMIN_PANEL=" value; changed=1; next }
      { print }
      END { if (changed==0) print "ADMIN_PANEL=" value }
    ' "$ENV_FILE" > "$ENV_FILE.tmp"
    mv "$ENV_FILE.tmp" "$ENV_FILE"
    echo "Updated ADMIN_PANEL=$PANEL in .env"
  fi
else
  if [[ "$DRY_RUN" == "true" ]]; then
    echo "[dry-run] Would append ADMIN_PANEL=$PANEL to .env"
  else
    echo "ADMIN_PANEL=$PANEL" >> "$ENV_FILE"
    echo "Added ADMIN_PANEL=$PANEL to .env"
  fi
fi

if [[ "$DRY_RUN" == "true" ]]; then
  echo "[dry-run] Would run: php artisan config:clear"
  echo "[dry-run] Would run: php artisan route:clear"
  exit 0
fi

(
  cd "$ROOT_DIR"
  php artisan config:clear
  php artisan route:clear
)

echo "Admin panel switched to '$PANEL'."

