# 🚀 SID Lite - Quick Start Guide

One-command installation untuk Ubuntu 24.04 LTS.

## ⚡ Instalasi Super Cepat

```bash
# 1. Clone atau download project
git clone <repository-url> sid-lite
cd sid-lite

# 2. Jalankan scripts berurutan
cd scripts
chmod +x *.sh

./install-dependencies.sh  # ~10 menit
./setup-mysql.sh          # ~2 menit
./setup-project.sh        # ~5 menit
./test-api.sh             # ~1 menit
```

**Total waktu: ~20 menit** (tergantung internet)

## 🎯 Setelah Instalasi

### Login Web Interface
```
URL: http://localhost/login

Admin:
  Email: admin@sid.com
  Password: password

Operator:
  Email: operator@sid.com
  Password: password

Viewer:
  Email: viewer@sid.com
  Password: password
```

### Test API dengan cURL

```bash
# Get modules
curl http://localhost/api/v1/modules | jq '.'

# Search warga
curl 'http://localhost/api/v1/warga/search?q=Siti' | jq '.'

# Login API
curl -X POST http://localhost/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sid.com",
    "password": "password"
  }' | jq '.'
```

## 📚 Pages Available

| URL | Description |
|-----|-------------|
| `/login` | Login page |
| `/dashboard` | Dashboard dengan statistics |
| `/form` | Form pendataan warga |
| `/users` | User management (Admin only) |

## 📡 API Endpoints

Base URL: `http://localhost/api/v1`

- `GET /modules` - List all modules
- `GET /modules/{code}` - Get module with questions
- `GET /warga/search?q=...` - Search warga
- `GET /warga/{nik}` - Get single warga
- `POST /responses` - Save module response
- `GET /dashboard/stats` - Dashboard statistics

**Full API documentation**: See `LARAVEL_STRUCTURE.md`

## 🔧 Useful Commands

```bash
# View logs
tail -f /var/www/sid-lite/laravel-backend/storage/logs/laravel.log

# Restart Apache
sudo systemctl restart apache2

# Check MySQL
sudo systemctl status mysql

# Clear Laravel cache
cd /var/www/sid-lite/laravel-backend
php artisan cache:clear
```

## 🗑️ Uninstall

```bash
cd scripts
./cleanup.sh
```

## 📖 Full Documentation

- **scripts/README.md** - Detailed setup instructions
- **SETUP_MYSQL_AUTH.md** - MySQL & Auth configuration
- **LARAVEL_STRUCTURE.md** - Backend architecture
- **scripts/curl-examples.sh** - API testing examples

## ⚠️ Important

**Change default passwords before production!**

```bash
# Login as admin
http://localhost/login

# Go to "Kelola User"
# Update all user passwords
```

## 🆘 Troubleshooting

### Can't access http://localhost
```bash
# Check Apache
sudo systemctl status apache2
sudo systemctl restart apache2
```

### Database connection error
```bash
# Check MySQL
sudo systemctl status mysql
mysql -u sid_user -psid_password_2024
```

### Permission denied
```bash
cd /var/www/sid-lite/laravel-backend
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage bootstrap/cache
```

---

**Selamat menggunakan SID Lite! 🎉**

For issues or questions, check the documentation files or run `./test-api.sh` to diagnose problems.
