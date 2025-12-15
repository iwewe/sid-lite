#!/bin/bash

#############################################
# SID Lite - Laravel Project Setup
# Ubuntu 24.04 LTS
# Description: Setup Laravel project and run migrations
#############################################

set -e  # Exit on error

echo "=========================================="
echo "🚀 SID Lite - Laravel Project Setup"
echo "=========================================="
echo ""

# Configuration
PROJECT_NAME="sid-lite"
PROJECT_DIR="/var/www/$PROJECT_NAME"
LARAVEL_DIR="$PROJECT_DIR/laravel-backend"

# Database credentials (must match setup-mysql.sh)
DB_NAME="sid_lite"
DB_USER="sid_user"
DB_PASSWORD="sid_password_2024"

echo "📂 Project Configuration:"
echo "   Project: $PROJECT_NAME"
echo "   Directory: $LARAVEL_DIR"
echo ""

# Check if running from sid-lite directory
if [ ! -d "./laravel-backend" ]; then
    echo "❌ Error: Please run this script from the sid-lite root directory"
    echo "   Current directory: $(pwd)"
    echo "   Expected: /path/to/sid-lite/"
    exit 1
fi

echo "📦 Step 1: Creating project directory..."
sudo mkdir -p $PROJECT_DIR
sudo chown -R $USER:$USER $PROJECT_DIR

echo ""
echo "📋 Step 2: Copying files to project directory..."
cp -r ./laravel-backend $PROJECT_DIR/
cp ./mockup.html $PROJECT_DIR/laravel-backend/public/

cd $LARAVEL_DIR

echo ""
echo "🎼 Step 3: Installing Composer dependencies..."
composer install --optimize-autoloader --no-dev

echo ""
echo "🔧 Step 4: Setting up environment file..."
cp .env.example .env

# Update .env with database credentials
sed -i "s/DB_DATABASE=sid_lite/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=root/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/" .env

echo ""
echo "🔑 Step 5: Generating application key..."
php artisan key:generate

echo ""
echo "🗄️  Step 6: Running database migrations..."
php artisan migrate --force

echo ""
echo "🌱 Step 7: Seeding database with initial data..."
php artisan db:seed --force

echo ""
echo "🔒 Step 8: Setting permissions..."
sudo chown -R www-data:www-data $LARAVEL_DIR
sudo chmod -R 755 $LARAVEL_DIR
sudo chmod -R 775 $LARAVEL_DIR/storage
sudo chmod -R 775 $LARAVEL_DIR/bootstrap/cache

echo ""
echo "⚡ Step 9: Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "🌐 Step 10: Configuring Apache Virtual Host..."

# Create Apache virtual host configuration
sudo tee /etc/apache2/sites-available/$PROJECT_NAME.conf > /dev/null <<EOF
<VirtualHost *:80>
    ServerName localhost
    ServerAlias sid-lite.local
    DocumentRoot $LARAVEL_DIR/public

    <Directory $LARAVEL_DIR/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/$PROJECT_NAME-error.log
    CustomLog \${APACHE_LOG_DIR}/$PROJECT_NAME-access.log combined
</VirtualHost>
EOF

# Enable site and disable default
sudo a2dissite 000-default.conf
sudo a2ensite $PROJECT_NAME.conf

# Restart Apache
sudo systemctl restart apache2

echo ""
echo "=========================================="
echo "✅ Laravel Project Setup Complete!"
echo "=========================================="
echo ""
echo "📊 Setup Summary:"
echo "   Project Directory: $LARAVEL_DIR"
echo "   Database: $DB_NAME"
echo "   Public URL: http://localhost"
echo ""
echo "🔑 Default Login Credentials:"
echo "   Admin: admin@sid.com / password"
echo "   Operator: operator@sid.com / password"
echo "   Viewer: viewer@sid.com / password"
echo ""
echo "🌐 Available URLs:"
echo "   Login: http://localhost/login"
echo "   Dashboard: http://localhost/dashboard"
echo "   Form: http://localhost/form"
echo "   API Docs: http://localhost/api/v1/modules"
echo ""
echo "🧪 Test the installation:"
echo "   Run: ./test-api.sh"
echo ""
echo "⚠️  IMPORTANT Security Steps:"
echo "   1. Change default user passwords!"
echo "   2. Update DB_PASSWORD in production"
echo "   3. Set APP_DEBUG=false in .env for production"
echo "   4. Setup SSL certificate (Let's Encrypt)"
echo ""
