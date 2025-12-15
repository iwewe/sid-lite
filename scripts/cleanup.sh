#!/bin/bash

#############################################
# SID Lite - Cleanup Script
# Description: Remove installation and reset system
#############################################

set -e

echo "=========================================="
echo "🗑️  SID Lite - Cleanup Script"
echo "=========================================="
echo ""

RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${RED}⚠️  WARNING: This will remove SID Lite installation!${NC}"
echo ""
echo "This script will:"
echo "  - Remove project files from /var/www/sid-lite"
echo "  - Drop sid_lite database"
echo "  - Remove database user sid_user"
echo "  - Disable Apache virtual host"
echo "  - Clear Laravel cache"
echo ""
echo -e "${YELLOW}Note: This will NOT remove PHP, MySQL, Apache, or Composer${NC}"
echo ""
read -p "Are you sure you want to continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Cleanup cancelled."
    exit 0
fi

echo ""
echo "🔐 Enter MySQL root password to proceed:"

# Drop database and user
mysql -u root -p <<MYSQL_SCRIPT
DROP DATABASE IF EXISTS sid_lite;
DROP USER IF EXISTS 'sid_user'@'localhost';
DROP USER IF EXISTS 'sid_user'@'%';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

echo "✅ Database and user removed"

# Disable Apache site
if [ -f /etc/apache2/sites-enabled/sid-lite.conf ]; then
    sudo a2dissite sid-lite.conf
    echo "✅ Apache virtual host disabled"
fi

# Remove Apache config
if [ -f /etc/apache2/sites-available/sid-lite.conf ]; then
    sudo rm /etc/apache2/sites-available/sid-lite.conf
    echo "✅ Apache configuration removed"
fi

# Remove project directory
if [ -d /var/www/sid-lite ]; then
    sudo rm -rf /var/www/sid-lite
    echo "✅ Project files removed"
fi

# Restart Apache
sudo systemctl restart apache2
echo "✅ Apache restarted"

echo ""
echo "=========================================="
echo "✅ Cleanup Complete!"
echo "=========================================="
echo ""
echo "Removed:"
echo "  - Project: /var/www/sid-lite"
echo "  - Database: sid_lite"
echo "  - User: sid_user"
echo "  - Apache config: sid-lite.conf"
echo ""
echo "Still installed (not removed):"
echo "  - PHP"
echo "  - Composer"
echo "  - MySQL Server"
echo "  - Apache Web Server"
echo "  - Node.js"
echo ""
echo "To reinstall, run:"
echo "  ./setup-mysql.sh"
echo "  ./setup-project.sh"
echo ""
