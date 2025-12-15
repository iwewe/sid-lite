# 🚀 SID Lite - Setup Scripts for Ubuntu 24.04

Automated installation and setup scripts for SID Lite backend system.

## 📋 Prerequisites

- Fresh Ubuntu 24.04 LTS installation
- User with sudo privileges
- Internet connection
- At least 2GB RAM
- 10GB free disk space

## 🎯 Quick Start

### One-Command Installation

```bash
# Clone or download the project first
cd /path/to/sid-lite

# Make scripts executable
chmod +x scripts/*.sh

# Run installation in sequence
cd scripts
./install-dependencies.sh    # Install PHP, MySQL, Apache, etc
./setup-mysql.sh             # Setup MySQL database
./setup-project.sh           # Setup Laravel project
./test-api.sh                # Test API endpoints
```

## 📦 Script Details

### 1. install-dependencies.sh

**Purpose**: Install all system dependencies

**What it installs**:
- PHP 8.2 + extensions (MySQL, mbstring, XML, curl, etc)
- Composer (latest version)
- MySQL Server 8.0
- Apache Web Server
- Node.js 20.x + NPM
- Essential utilities (git, curl, wget, unzip)

**Usage**:
```bash
./install-dependencies.sh
```

**Duration**: ~10-15 minutes (depends on internet speed)

**Note**: You will be prompted to set MySQL root password during installation.

---

### 2. setup-mysql.sh

**Purpose**: Create database and user for SID Lite

**What it does**:
- Creates `sid_lite` database with utf8mb4 charset
- Creates `sid_user` with password `sid_password_2024`
- Grants all privileges on sid_lite database
- Enables local and remote access
- Tests database connection

**Usage**:
```bash
./setup-mysql.sh
```

**Configuration**:
Edit the script to change default credentials:
```bash
DB_NAME="sid_lite"
DB_USER="sid_user"
DB_PASSWORD="sid_password_2024"  # ⚠️ Change this!
```

**Security**:
- Change the password before production use!
- Remote access is enabled by default (`'%'` host)
- To restrict to specific IP, edit the script

---

### 3. setup-project.sh

**Purpose**: Setup Laravel project and configure Apache

**What it does**:
- Copies project files to `/var/www/sid-lite/`
- Installs Composer dependencies
- Creates and configures `.env` file
- Generates application key
- Runs database migrations
- Seeds database with initial data (3 users, 3 modules, 5 warga)
- Sets proper file permissions
- Optimizes Laravel (cache config, routes, views)
- Creates Apache virtual host
- Restarts Apache

**Usage**:
```bash
# Must run from sid-lite root directory
cd /path/to/sid-lite/scripts
./setup-project.sh
```

**Output**:
- Project installed at: `/var/www/sid-lite/laravel-backend/`
- Accessible at: `http://localhost/`
- Admin login: `admin@sid.com / password`

---

### 4. test-api.sh

**Purpose**: Test all API endpoints with curl

**What it tests**:
- GET /api/v1/modules
- GET /api/v1/modules/{code}
- GET /api/v1/warga/search
- GET /api/v1/warga/{nik}
- POST /api/v1/responses
- GET /api/v1/warga/{nik}/responses
- GET /api/v1/dashboard/stats
- POST /auth/login
- GET with Bearer token authentication

**Usage**:
```bash
./test-api.sh
```

**Requirements**:
- `jq` installed (for JSON formatting)
  ```bash
  sudo apt install jq
  ```

---

## 🔍 Manual Installation Steps

If you prefer manual installation:

### Step 1: Install Dependencies
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Add PHP repository
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update

# Install PHP 8.2
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-mysql \
  php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath \
  php8.2-gd php8.2-intl php8.2-readline php8.2-tokenizer

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Apache
sudo apt install -y apache2
sudo a2enmod rewrite ssl headers
```

### Step 2: Create Database
```bash
mysql -u root -p
```

```sql
CREATE DATABASE sid_lite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'sid_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON sid_lite.* TO 'sid_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Setup Laravel Project
```bash
# Copy project files
sudo mkdir -p /var/www/sid-lite
sudo cp -r ./laravel-backend /var/www/sid-lite/
cd /var/www/sid-lite/laravel-backend

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
php artisan key:generate

# Edit .env file
nano .env
# Update DB_* credentials

# Run migrations
php artisan migrate --seed

# Set permissions
sudo chown -R www-data:www-data /var/www/sid-lite
sudo chmod -R 755 /var/www/sid-lite
sudo chmod -R 775 storage bootstrap/cache

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Configure Apache
```bash
sudo nano /etc/apache2/sites-available/sid-lite.conf
```

```apache
<VirtualHost *:80>
    ServerName localhost
    DocumentRoot /var/www/sid-lite/laravel-backend/public

    <Directory /var/www/sid-lite/laravel-backend/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/sid-lite-error.log
    CustomLog ${APACHE_LOG_DIR}/sid-lite-access.log combined
</VirtualHost>
```

```bash
sudo a2dissite 000-default.conf
sudo a2ensite sid-lite.conf
sudo systemctl restart apache2
```

---

## 📡 API Testing with cURL

### Basic Endpoints

#### Get All Modules
```bash
curl -X GET http://localhost/api/v1/modules | jq '.'
```

#### Get Module with Questions
```bash
curl -X GET http://localhost/api/v1/modules/jamban | jq '.'
```

#### Search Warga
```bash
# By name
curl -X GET 'http://localhost/api/v1/warga/search?q=Siti' | jq '.'

# By NIK
curl -X GET 'http://localhost/api/v1/warga/search?q=3173' | jq '.'

# By wilayah
curl -X GET 'http://localhost/api/v1/warga/search?dusun=Dusun+I&rw=02' | jq '.'
```

#### Get Single Warga
```bash
curl -X GET http://localhost/api/v1/warga/3173010101010001 | jq '.'
```

#### Create New Warga
```bash
curl -X POST http://localhost/api/v1/warga \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010099",
    "nama": "Test User",
    "dusun": "Dusun I",
    "rw": "01",
    "rt": "01",
    "alamat": "Jl. Test No. 123"
  }' | jq '.'
```

#### Save Module Response
```bash
curl -X POST http://localhost/api/v1/responses \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010001",
    "module_code": "jamban",
    "responses": {
      "b3r301a": "1",
      "b3r309a": "1",
      "b3r309b": "2",
      "b3r310": "1"
    },
    "submit": true
  }' | jq '.'
```

#### Get Warga Responses
```bash
# All modules
curl -X GET http://localhost/api/v1/warga/3173010101010001/responses | jq '.'

# Specific module
curl -X GET http://localhost/api/v1/warga/3173010101010001/responses/jamban | jq '.'
```

#### Get Dashboard Statistics
```bash
curl -X GET http://localhost/api/v1/dashboard/stats | jq '.'
```

---

### Authentication Endpoints

#### Login (Get Token)
```bash
curl -X POST http://localhost/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sid.com",
    "password": "password"
  }' | jq '.'
```

**Response**:
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@sid.com",
      "role": "admin"
    },
    "token": "1|AbCdEfGhIjKlMnOpQrStUvWxYz..."
  }
}
```

#### Use Token for Authenticated Requests
```bash
# Save token to variable
TOKEN="1|AbCdEfGhIjKlMnOpQrStUvWxYz..."

# Use token in requests
curl -X GET http://localhost/api/v1/modules \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

#### Logout
```bash
curl -X POST http://localhost/auth/logout \
  -H "Authorization: Bearer $TOKEN" | jq '.'
```

---

## 🔧 Troubleshooting

### Script Fails: "Permission Denied"
```bash
chmod +x scripts/*.sh
```

### MySQL Connection Error
```bash
# Test MySQL connection
mysql -u sid_user -p
# Enter password: sid_password_2024

# Check MySQL service
sudo systemctl status mysql

# Restart MySQL
sudo systemctl restart mysql
```

### Apache Shows 403 Forbidden
```bash
# Check permissions
sudo chown -R www-data:www-data /var/www/sid-lite
sudo chmod -R 755 /var/www/sid-lite
sudo chmod -R 775 /var/www/sid-lite/laravel-backend/storage

# Check Apache configuration
sudo apache2ctl configtest
sudo systemctl restart apache2
```

### 500 Internal Server Error
```bash
# Check Laravel logs
tail -f /var/www/sid-lite/laravel-backend/storage/logs/laravel.log

# Check Apache logs
sudo tail -f /var/log/apache2/sid-lite-error.log

# Clear Laravel cache
cd /var/www/sid-lite/laravel-backend
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### API Returns Empty Response
```bash
# Check if service is running
curl -I http://localhost

# Check API endpoint
curl -v http://localhost/api/v1/modules
```

---

## 🔐 Security Checklist

Before deploying to production:

- [ ] Change MySQL root password
- [ ] Change `sid_user` database password
- [ ] Update all default user passwords in application
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Restrict database user to specific IP (not `'%'`)
- [ ] Install SSL certificate (Let's Encrypt)
- [ ] Enable firewall: `sudo ufw enable`
- [ ] Setup regular database backups
- [ ] Disable directory listing in Apache
- [ ] Setup fail2ban for SSH protection

---

## 📊 Monitoring

### Check Services Status
```bash
# MySQL
sudo systemctl status mysql

# Apache
sudo systemctl status apache2

# Disk space
df -h

# Memory usage
free -h
```

### View Logs
```bash
# Laravel logs
tail -f /var/www/sid-lite/laravel-backend/storage/logs/laravel.log

# Apache access log
sudo tail -f /var/log/apache2/sid-lite-access.log

# Apache error log
sudo tail -f /var/log/apache2/sid-lite-error.log

# MySQL logs
sudo tail -f /var/log/mysql/error.log
```

---

## 📚 Additional Resources

- [SETUP_MYSQL_AUTH.md](../SETUP_MYSQL_AUTH.md) - MySQL & Authentication setup guide
- [LARAVEL_STRUCTURE.md](../LARAVEL_STRUCTURE.md) - Backend architecture guide
- [README.md](../laravel-backend/README.md) - Laravel backend documentation

---

## 🆘 Support

If you encounter issues:

1. Check logs (Laravel, Apache, MySQL)
2. Run test-api.sh to diagnose API issues
3. Verify file permissions
4. Check firewall settings
5. Consult documentation files

---

**Created for SID Lite v1.0**
