---
# Deployment / Permissions script for Habrin CMS

This script shows recommended commands to set file ownership and permissions before deploying to production.

Save this as `scripts/deploy.sh` and run it (edit user/group and paths as appropriate).

---

#!/usr/bin/env bash
set -euo pipefail

# Replace these with your server user and group
WEB_USER="www-data"
WEB_GROUP="www-data"
ROOT_DIR="$(pwd)"

echo "Setting ownership to $WEB_USER:$WEB_GROUP for content/ and logs/"
sudo chown -R "$WEB_USER":"$WEB_GROUP" "$ROOT_DIR/content" "$ROOT_DIR/logs"

echo "Setting directory permissions (directories: 750, files: 640)"
find "$ROOT_DIR/content" -type d -exec chmod 750 {} \;
find "$ROOT_DIR/content" -type f -exec chmod 640 {} \;

# Logs should be writable by the web user
chmod -R 750 "$ROOT_DIR/logs"

# Ensure public files are readable
find "$ROOT_DIR" -type f -name "*.php" -exec chmod 640 {} \; || true
find "$ROOT_DIR" -type f -name "*.html" -exec chmod 644 {} \; || true

echo "Deployment permissions applied."
