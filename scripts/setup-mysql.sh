#!/bin/bash

#############################################
# SID Lite - MySQL Database Setup
# Ubuntu 24.04 LTS
# Description: Create database and user for SID Lite
#############################################

set -e  # Exit on error

echo "=========================================="
echo "🗄️  SID Lite - MySQL Setup"
echo "=========================================="
echo ""

# Configuration
DB_NAME="sid_lite"
DB_USER="sid_user"
DB_PASSWORD="sid_password_2024"  # Change this!

echo "📝 Database Configuration:"
echo "   Database Name: $DB_NAME"
echo "   Username: $DB_USER"
echo "   Password: $DB_PASSWORD"
echo ""
echo "⚠️  IMPORTANT: Change the password in this script before running in production!"
echo ""
read -p "Press ENTER to continue or CTRL+C to cancel..."

echo ""
echo "🔐 Please enter your MySQL root password:"

# Create database and user
mysql -u root -p <<MYSQL_SCRIPT
-- Create database
DROP DATABASE IF EXISTS $DB_NAME;
CREATE DATABASE $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user and grant privileges
DROP USER IF EXISTS '$DB_USER'@'localhost';
CREATE USER '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';

-- For remote access (optional)
DROP USER IF EXISTS '$DB_USER'@'%';
CREATE USER '$DB_USER'@'%' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'%';

FLUSH PRIVILEGES;

-- Show databases and users
SHOW DATABASES;
SELECT User, Host FROM mysql.user WHERE User = '$DB_USER';
MYSQL_SCRIPT

echo ""
echo "✅ Database setup completed!"
echo ""
echo "📊 Database Information:"
echo "   Database: $DB_NAME"
echo "   User: $DB_USER"
echo "   Password: $DB_PASSWORD"
echo ""
echo "🔒 Security Note:"
echo "   - Change the password after setup"
echo "   - Remote access is enabled ('%' host)"
echo "   - To restrict, use specific IP: '$DB_USER'@'192.168.1.100'"
echo ""

# Test connection
echo "🧪 Testing database connection..."
mysql -u $DB_USER -p$DB_PASSWORD -e "USE $DB_NAME; SELECT 'Connection successful!' AS Status;"

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Database connection test successful!"
    echo ""
    echo "📝 Update your .env file with these credentials:"
    echo ""
    echo "DB_CONNECTION=mysql"
    echo "DB_HOST=127.0.0.1"
    echo "DB_PORT=3306"
    echo "DB_DATABASE=$DB_NAME"
    echo "DB_USERNAME=$DB_USER"
    echo "DB_PASSWORD=$DB_PASSWORD"
    echo ""
else
    echo ""
    echo "❌ Database connection test failed!"
    echo "   Please check MySQL credentials and try again."
    exit 1
fi

echo "🎯 Next step: Run setup-project.sh to setup Laravel project"
echo ""
