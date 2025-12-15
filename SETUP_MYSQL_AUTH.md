# 🔐 Setup Authentication & MySQL Remote Server

## 📋 Prerequisites

- PHP >= 8.1
- Composer
- MySQL 5.7+ atau MariaDB 10.3+
- Web Server (Apache/Nginx)

## 🗄️ MySQL Remote Server Setup

### 1. Konfigurasi Database di Remote Server

#### Create Database
```sql
CREATE DATABASE sid_lite CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### Create User & Grant Privileges
```sql
-- Ganti 'your_ip' dengan IP server aplikasi atau '%' untuk allow dari mana saja
CREATE USER 'sid_user'@'your_ip' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON sid_lite.* TO 'sid_user'@'your_ip';
FLUSH PRIVILEGES;
```

#### Allow Remote Connections
Edit MySQL config file (`/etc/mysql/mysql.conf.d/mysqld.cnf`):
```ini
# Comment atau ubah bind-address
# bind-address = 127.0.0.1
bind-address = 0.0.0.0
```

Restart MySQL:
```bash
sudo systemctl restart mysql
```

#### Firewall Configuration
```bash
# Ubuntu/Debian
sudo ufw allow 3306/tcp

# CentOS/RHEL
sudo firewall-cmd --add-port=3306/tcp --permanent
sudo firewall-cmd --reload
```

### 2. Konfigurasi Laravel untuk MySQL Remote

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=your-remote-server.com  # atau IP: 192.168.1.100
DB_PORT=3306
DB_DATABASE=sid_lite
DB_USERNAME=sid_user
DB_PASSWORD=strong_password_here
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### 3. Test Koneksi Database
```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::select('SELECT VERSION()');
```

## 🚀 Installation Steps

### 1. Clone/Copy Project
```bash
cd /var/www/
git clone <repository> sid-lite
cd sid-lite/laravel-backend
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env` dengan credentials MySQL remote Anda (lihat section di atas).

### 5. Run Migrations & Seeders
```bash
php artisan migrate --seed
```

Output yang diharapkan:
```
✅ 3 Users seeded successfully
   - Admin: admin@sid.com / password
   - Operator: operator@sid.com / password
   - Viewer: viewer@sid.com / password
✅ 3 modules seeded successfully
✅ 5 warga seeded successfully
🎉 Database seeding completed!
```

### 6. Setup Permissions
```bash
sudo chown -R www-data:www-data /var/www/sid-lite
sudo chmod -R 755 /var/www/sid-lite
sudo chmod -R 775 /var/www/sid-lite/laravel-backend/storage
sudo chmod -R 775 /var/www/sid-lite/laravel-backend/bootstrap/cache
```

### 7. Configure Web Server

#### Apache Configuration
```apache
<VirtualHost *:80>
    ServerName sid-lite.example.com
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

Enable site:
```bash
sudo a2ensite sid-lite.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name sid-lite.example.com;
    root /var/www/sid-lite/laravel-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/sid-lite /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 8. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 👥 User Roles & Permissions

### Admin
- **Full access** ke semua fitur
- Bisa manage users (create, edit, delete, activate/deactivate)
- Bisa manage modules
- Bisa input dan edit data
- Bisa lihat dashboard statistics

### Operator
- Bisa input dan edit data warga
- Bisa lihat dashboard
- **Tidak bisa** manage users
- **Tidak bisa** manage modules

### Viewer
- **Read-only** access
- Bisa lihat dashboard
- **Tidak bisa** input/edit data
- **Tidak bisa** manage users/modules

## 🔑 Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@sid.com | password |
| Operator | operator@sid.com | password |
| Viewer | viewer@sid.com | password |

⚠️ **PENTING**: Ganti password default setelah login pertama kali!

## 🌐 Accessing the Application

### Web Interface
```
http://your-domain.com/login
```

### API Endpoints
```
http://your-domain.com/api/v1/...
```

## 📱 API Authentication (untuk Mobile App)

### Login
```bash
curl -X POST http://your-domain.com/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sid.com",
    "password": "password"
  }'
```

Response:
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
    "token": "1|AbCdEf..."
  }
}
```

### Gunakan Token untuk API Calls
```bash
curl http://your-domain.com/api/v1/modules \
  -H "Authorization: Bearer 1|AbCdEf..."
```

## 🛠️ Troubleshooting

### Error: "SQLSTATE[HY000] [2002] Connection refused"
```bash
# Check MySQL service
sudo systemctl status mysql

# Check firewall
sudo ufw status

# Test connection from app server
mysql -h remote-server.com -u sid_user -p
```

### Error: "SQLSTATE[HY000] [1045] Access denied"
```sql
-- Check user privileges
SHOW GRANTS FOR 'sid_user'@'%';

-- Recreate user if needed
DROP USER 'sid_user'@'%';
CREATE USER 'sid_user'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON sid_lite.* TO 'sid_user'@'%';
```

### Error: "Class 'X' not found"
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Permission Denied Errors
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

## 🔒 Security Best Practices

1. **Ganti Default Passwords**
   - Login sebagai admin
   - Pergi ke "Kelola User"
   - Update password semua user

2. **Restrict Database Access**
   ```sql
   -- Gunakan specific IP, bukan '%'
   CREATE USER 'sid_user'@'192.168.1.100' IDENTIFIED BY 'password';
   ```

3. **Use HTTPS in Production**
   ```bash
   # Install Certbot
   sudo apt install certbot python3-certbot-apache
   sudo certbot --apache -d sid-lite.example.com
   ```

4. **Enable APP_DEBUG=false**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

5. **Backup Database Regularly**
   ```bash
   # Create backup script
   mysqldump -h remote-server.com -u sid_user -p sid_lite > backup_$(date +%Y%m%d).sql
   ```

## 📊 Monitoring

### Check Application Logs
```bash
tail -f storage/logs/laravel.log
```

### Check Database Connections
```sql
SHOW PROCESSLIST;
```

### Check Disk Space
```bash
df -h
du -sh /var/www/sid-lite
```

## 🆘 Support

Jika ada masalah:
1. Check logs di `storage/logs/laravel.log`
2. Check web server logs (`/var/log/apache2/` atau `/var/log/nginx/`)
3. Check MySQL logs
4. Hubungi developer atau buat issue di GitHub

---

**Dokumentasi ini dibuat untuk SID Lite Backend v1.0**
