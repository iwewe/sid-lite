# 🏛️ SID Lite - Laravel Backend

Backend API untuk Sistem Informasi Desa (SID) Lite - Form Pendataan Sarana Prasarana Rumah Tinggal.

## 📋 Features

✅ **Dynamic Module System** - Tambah modul baru tanpa coding
✅ **Flexible Questions** - Setiap modul bisa punya pertanyaan berbeda-beda
✅ **Automatic Scoring** - Verification score calculated automatically
✅ **Search & Filter** - Cari warga by NIK, nama, atau wilayah
✅ **REST API** - Clean API design dengan JSON responses
✅ **Database Migrations** - Version control untuk database schema
✅ **Seeders** - Dummy data untuk development

## 🚀 Quick Start

### Prerequisites

- PHP >= 8.1
- Composer
- PostgreSQL atau MySQL
- Laravel 10.x

### Installation

1. **Clone atau copy project ini**
```bash
cd sid-lite/laravel-backend
```

2. **Install dependencies**
```bash
composer install
```

3. **Copy environment file**
```bash
cp .env.example .env
```

4. **Generate application key**
```bash
php artisan key:generate
```

5. **Setup database di `.env`**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sid_lite
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

6. **Run migrations & seeders**
```bash
php artisan migrate --seed
```

7. **Start development server**
```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

## 📡 API Endpoints

Base URL: `http://localhost:8000/api/v1`

### Warga

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/warga/search?q=...&dusun=...&rw=...&rt=...` | Search warga |
| GET | `/warga/{nik}` | Get single warga |
| POST | `/warga` | Create new warga |
| PUT | `/warga/{nik}` | Update warga |
| DELETE | `/warga/{nik}` | Delete warga |
| GET | `/warga/{nik}/responses` | Get all responses for warga |
| GET | `/warga/{nik}/responses/{module_code}` | Get response for specific module |

### Modules

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/modules` | List all active modules |
| GET | `/modules/{code}` | Get module with questions |
| POST | `/modules` | Create new module (admin) |
| PUT | `/modules/{code}` | Update module |
| DELETE | `/modules/{code}` | Deactivate module |

### Responses

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/responses` | Save/update response |
| DELETE | `/responses/{id}` | Delete response |

### Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/dashboard/stats` | Get statistics |

## 📝 API Examples

### 1. Search Warga

```bash
curl "http://localhost:8000/api/v1/warga/search?q=Siti"
```

Response:
```json
{
  "success": true,
  "message": "Data warga berhasil diambil",
  "data": [
    {
      "nik": "3173010101010001",
      "nama": "Siti Aminah",
      "dusun": "Dusun I",
      "rw": "02",
      "rt": "01",
      "alamat": "Jl. Merdeka No. 12"
    }
  ],
  "count": 1
}
```

### 2. Get Module with Questions

```bash
curl "http://localhost:8000/api/v1/modules/jamban"
```

Response:
```json
{
  "success": true,
  "data": {
    "code": "jamban",
    "name": "Jamban Septic",
    "min_verified": 4,
    "questions": [
      {
        "code": "b3r301a",
        "question": "Status kepemilikan bangunan...",
        "field_type": "select",
        "options": [
          {"value": "1", "label": "1. Milik sendiri"},
          ...
        ],
        "is_required": true
      }
    ]
  }
}
```

### 3. Save Response (dari mockup.html)

```bash
curl -X POST "http://localhost:8000/api/v1/responses" \
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
  }'
```

Response:
```json
{
  "success": true,
  "message": "Data berhasil disimpan ke database",
  "data": {
    "id": 1,
    "verification_score": 4,
    "is_verified": true,
    "min_verified": 4,
    "submitted_at": "2025-01-15 10:30:00"
  }
}
```

## 🎯 Connect Frontend (mockup.html)

Edit `mockup.html` line ~767 (di function `submitToDatabase`):

```javascript
// Ganti ini:
// await fetch('/api/save', { method: 'POST', body: JSON.stringify(payload) });

// Dengan:
await fetch('http://localhost:8000/api/v1/responses', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    nik: selectedWarga.nik,
    module_code: module, // 'jamban', 'rtlh', atau 'pah'
    responses: payload, // object dengan question_code: value
    submit: true, // true = submit final, false = draft
  }),
});
```

## 🔧 How to Add New Module

### Option 1: Via Seeder (Recommended)

Create `database/seeders/StuntingModuleSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\ModuleQuestion;

class StuntingModuleSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::create([
            'code' => 'stunting',
            'name' => 'Data Stunting',
            'description' => 'Pemantauan stunting balita',
            'min_verified' => 3,
            'is_active' => true,
            'order' => 4,
            'icon' => '👶',
        ]);

        $questions = [
            [
                'code' => 'st001',
                'question' => 'Usia anak (bulan)',
                'field_type' => 'number',
                'is_required' => true,
                'order' => 0,
            ],
            [
                'code' => 'st002',
                'question' => 'Berat badan (kg)',
                'field_type' => 'number',
                'is_required' => true,
                'order' => 1,
            ],
            [
                'code' => 'st003',
                'question' => 'Tinggi badan (cm)',
                'field_type' => 'number',
                'is_required' => true,
                'order' => 2,
            ],
        ];

        foreach ($questions as $question) {
            ModuleQuestion::create(array_merge([
                'module_id' => $module->id
            ], $question));
        }
    }
}
```

Run seeder:
```bash
php artisan db:seed --class=StuntingModuleSeeder
```

### Option 2: Via API

```bash
curl -X POST "http://localhost:8000/api/v1/modules" \
  -H "Content-Type: application/json" \
  -d '{
    "code": "stunting",
    "name": "Data Stunting",
    "min_verified": 3,
    "icon": "👶",
    "questions": [
      {
        "code": "st001",
        "question": "Usia anak (bulan)",
        "field_type": "number",
        "is_required": true
      },
      {
        "code": "st002",
        "question": "Berat badan (kg)",
        "field_type": "number",
        "is_required": true
      }
    ]
  }'
```

## 📊 Database Schema

### Tables

- `warga` - Data warga
- `modules` - Master modul (jamban, rtlh, pah, etc)
- `module_questions` - Pertanyaan per modul
- `module_responses` - Jawaban warga
- `users` - Petugas/operator

### Key Relationships

```
Warga 1:N ModuleResponse N:1 Module 1:N ModuleQuestion
```

## 🔐 Authentication (Optional)

Laravel Sanctum sudah diinclude untuk API authentication. Uncomment di `routes/api.php` untuk enable.

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## 🧪 Testing

```bash
# Via CURL
curl "http://localhost:8000/api/v1/modules"

# Via Browser
http://localhost:8000/api/v1/modules
```

## 📦 Project Structure

```
laravel-backend/
├── app/
│   ├── Http/Controllers/API/
│   │   ├── WargaController.php
│   │   ├── ModuleController.php
│   │   └── ResponseController.php
│   └── Models/
│       ├── Warga.php
│       ├── Module.php
│       ├── ModuleQuestion.php
│       └── ModuleResponse.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── ModuleSeeder.php (3 modul: Jamban, RTLH, PAH)
│       └── WargaSeeder.php (5 dummy warga)
└── routes/
    └── api.php
```

## 🎨 Response Format

All API responses follow this format:

**Success:**
```json
{
  "success": true,
  "message": "...",
  "data": { ... }
}
```

**Error:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

## 🚧 Troubleshooting

### CORS Error

Jika frontend (mockup.html) di-host di domain berbeda, install Laravel CORS:

```bash
composer require fruitcake/laravel-cors
```

Edit `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'], // atau specific domain
```

### Database Connection Error

Pastikan PostgreSQL/MySQL running dan credentials di `.env` benar:

```bash
# Test koneksi
php artisan tinker
>>> DB::connection()->getPdo();
```

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [API Resources](https://laravel.com/docs/eloquent-resources)

## 🤝 Contributing

Untuk menambah fitur atau modul baru:
1. Buat migration untuk schema changes
2. Update models jika perlu
3. Tambah controller methods
4. Update routes
5. Create seeders untuk test data

## 📄 License

Open source untuk institusi pemerintahan daerah.

---

**Dibuat dengan ❤️ untuk SID Lite**
