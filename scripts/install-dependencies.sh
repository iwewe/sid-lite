#!/bin/bash

#############################################
# SID Lite - Installation Script
# Ubuntu 24.04 LTS
# Author: Claude AI
# Description: Install all dependencies for Laravel + MySQL
#############################################

set -e  # Exit on error

echo "=========================================="
echo "🏛️  SID Lite - Installation Script"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -eq 0 ]; then
   echo "❌ Please do not run as root. Run as normal user with sudo privileges."
   exit 1
fi

echo "📦 Step 1: Updating system packages..."
sudo apt update
sudo apt upgrade -y

echo ""
echo "📦 Step 2: Installing basic utilities..."
sudo apt install -y \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    lsb-release \
    gnupg \
    curl \
    wget \
    git \
    unzip \
    zip

echo ""
echo "🐘 Step 3: Installing PHP 8.2 and extensions..."
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y \
    php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-mysql \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-gd \
    php8.2-intl \
    php8.2-readline \
    php8.2-tokenizer

# Verify PHP installation
echo ""
echo "✅ PHP version installed:"
php -v

echo ""
echo "🎼 Step 4: Installing Composer..."
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
HASH=$(curl -sS https://composer.github.io/installer.sig)
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Verify Composer installation
echo ""
echo "✅ Composer version installed:"
composer --version

echo ""
echo "🗄️  Step 5: Installing MySQL Server..."
sudo apt install -y mysql-server mysql-client

# Start MySQL service
sudo systemctl start mysql
sudo systemctl enable mysql

echo ""
echo "✅ MySQL status:"
sudo systemctl status mysql --no-pager

echo ""
echo "🌐 Step 6: Installing Apache Web Server..."
sudo apt install -y apache2

# Enable required Apache modules
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Start Apache service
sudo systemctl start apache2
sudo systemctl enable apache2

echo ""
echo "✅ Apache status:"
sudo systemctl status apache2 --no-pager

echo ""
echo "🔧 Step 7: Configuring PHP for Laravel..."

# Get PHP version
PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")

# Update php.ini settings
sudo sed -i 's/upload_max_filesize = .*/upload_max_filesize = 64M/' /etc/php/$PHP_VERSION/apache2/php.ini
sudo sed -i 's/post_max_size = .*/post_max_size = 64M/' /etc/php/$PHP_VERSION/apache2/php.ini
sudo sed -i 's/memory_limit = .*/memory_limit = 256M/' /etc/php/$PHP_VERSION/apache2/php.ini
sudo sed -i 's/max_execution_time = .*/max_execution_time = 300/' /etc/php/$PHP_VERSION/apache2/php.ini

# Restart Apache to apply changes
sudo systemctl restart apache2

echo ""
echo "🔒 Step 8: Securing MySQL installation..."
echo ""
echo "⚠️  IMPORTANT: You will be prompted to set MySQL root password."
echo "   Please remember this password for database setup!"
echo ""
read -p "Press ENTER to continue with MySQL secure installation..."

sudo mysql_secure_installation

echo ""
echo "🎨 Step 9: Installing Node.js and NPM (optional, for frontend assets)..."
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

echo ""
echo "✅ Node.js version installed:"
node -v
npm -v

echo ""
echo "🔥 Step 10: Configuring Firewall..."
sudo ufw allow 'Apache Full'
sudo ufw allow 3306/tcp  # MySQL
sudo ufw allow 22/tcp    # SSH

# Don't enable firewall automatically to avoid locking out user
echo "⚠️  Firewall rules added but not enabled yet."
echo "   To enable firewall, run: sudo ufw enable"

echo ""
echo "=========================================="
echo "✅ Installation Complete!"
echo "=========================================="
echo ""
echo "📊 Installed versions:"
echo "   - PHP: $(php -v | head -n 1)"
echo "   - Composer: $(composer --version --no-ansi)"
echo "   - MySQL: $(mysql --version)"
echo "   - Apache: $(apache2 -v | head -n 1)"
echo "   - Node.js: $(node -v)"
echo "   - NPM: $(npm -v)"
echo ""
echo "🎯 Next steps:"
echo "   1. Run setup-mysql.sh to configure MySQL database"
echo "   2. Run setup-project.sh to setup Laravel project"
echo ""
echo "📚 Documentation:"
echo "   - SETUP_MYSQL_AUTH.md - MySQL & Authentication guide"
echo "   - LARAVEL_STRUCTURE.md - Backend structure guide"
echo ""
