# 🏛️ SID Lite - Laravel Backend Structure

## 📁 Struktur Project

```
sid-lite/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/
│   │   │   │   ├── WargaController.php
│   │   │   │   ├── ModuleController.php
│   │   │   │   └── ResponseController.php
│   │   │   └── DashboardController.php
│   │   ├── Requests/
│   │   │   ├── WargaSearchRequest.php
│   │   │   └── ModuleResponseRequest.php
│   │   └── Resources/
│   │       ├── WargaResource.php
│   │       └── ModuleResource.php
│   ├── Models/
│   │   ├── Warga.php
│   │   ├── Module.php
│   │   ├── ModuleQuestion.php
│   │   └── ModuleResponse.php
│   ├── Services/
│   │   ├── WargaService.php
│   │   └── ModuleService.php
│   └── Repositories/
│       ├── WargaRepository.php
│       └── ModuleRepository.php
├── database/
│   ├── migrations/
│   │   ├── 2025_01_01_000001_create_warga_table.php
│   │   ├── 2025_01_01_000002_create_modules_table.php
│   │   ├── 2025_01_01_000003_create_module_questions_table.php
│   │   └── 2025_01_01_000004_create_module_responses_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── WargaSeeder.php
│   │   └── ModuleSeeder.php
│   └── factories/
│       └── WargaFactory.php
├── routes/
│   ├── api.php
│   └── web.php
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       └── form.blade.php
├── config/
│   └── modules.php
└── public/
    └── mockup.html (existing)
```

## 🗄️ Database Schema

### Konsep Desain:
- **Scalable**: Mudah tambah modul baru tanpa migration
- **Flexible**: Questions dynamic per modul
- **Normalized**: Relational database best practices

### Tables:

#### 1. `warga` (Data Warga)
```sql
id (bigint, PK)
nik (string, unique, indexed)
nama (string, indexed)
dusun (string, nullable)
rw (string, nullable)
rt (string, nullable)
alamat (text, nullable)
no_kk (string, nullable)
created_at, updated_at
```

#### 2. `modules` (Master Modul)
```sql
id (bigint, PK)
code (string, unique) -- 'jamban', 'rtlh', 'pah'
name (string) -- 'Jamban Septic', 'RTLH', 'PAH'
description (text, nullable)
min_verified (integer) -- jumlah minimum pertanyaan wajib
is_active (boolean, default: true)
order (integer, default: 0) -- untuk sorting
created_at, updated_at
```

#### 3. `module_questions` (Pertanyaan per Modul)
```sql
id (bigint, PK)
module_id (bigint, FK -> modules.id)
code (string) -- 'b3r301a', 'b3r309a', etc
question (text) -- text pertanyaan
field_type (enum: 'select', 'text', 'number', 'date') -- tipe input
options (json, nullable) -- untuk select options
is_required (boolean, default: false)
order (integer, default: 0)
created_at, updated_at

Index: module_id, code
Unique: (module_id, code)
```

#### 4. `module_responses` (Jawaban Warga)
```sql
id (bigint, PK)
warga_id (bigint, FK -> warga.id)
module_id (bigint, FK -> modules.id)
responses (json) -- {question_code: value}
verification_score (integer)
is_verified (boolean, computed)
submitted_by (bigint, FK -> users.id, nullable)
submitted_at (datetime, nullable)
created_at, updated_at

Index: warga_id, module_id, submitted_at
Unique: (warga_id, module_id)
```

#### 5. `users` (Petugas/Admin)
```sql
id (bigint, PK)
name (string)
email (string, unique)
password (string)
role (enum: 'admin', 'operator', 'viewer')
created_at, updated_at
```

## 🔗 Relationships

```
Warga
  ├── hasMany(ModuleResponse)

Module
  ├── hasMany(ModuleQuestion)
  └── hasMany(ModuleResponse)

ModuleQuestion
  └── belongsTo(Module)

ModuleResponse
  ├── belongsTo(Warga)
  ├── belongsTo(Module)
  └── belongsTo(User, 'submitted_by')
```

## 🚀 API Endpoints

### Warga
```
GET    /api/warga/search?q=...&dusun=...&rw=...&rt=...
GET    /api/warga/{nik}
POST   /api/warga
PUT    /api/warga/{nik}
DELETE /api/warga/{nik}
```

### Modules
```
GET    /api/modules (list semua modul aktif)
GET    /api/modules/{code} (detail modul + questions)
POST   /api/modules (create modul baru - admin only)
PUT    /api/modules/{code}
DELETE /api/modules/{code}
```

### Module Questions
```
GET    /api/modules/{code}/questions
POST   /api/modules/{code}/questions (tambah pertanyaan)
PUT    /api/questions/{id}
DELETE /api/questions/{id}
```

### Responses
```
GET    /api/warga/{nik}/responses (semua jawaban warga)
GET    /api/warga/{nik}/responses/{module_code}
POST   /api/responses (simpan/update jawaban)
DELETE /api/responses/{id}
```

### Dashboard
```
GET    /api/dashboard/stats
GET    /api/dashboard/verification-summary
```

## 📝 JSON Response Format

### Success Response:
```json
{
  "success": true,
  "message": "Data berhasil disimpan",
  "data": { ... }
}
```

### Error Response:
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "nik": ["NIK wajib diisi"]
  }
}
```

## 🔐 Authentication

- Sanctum untuk API authentication
- Role-based access control (admin, operator, viewer)

## 🎯 Cara Menambah Modul Baru

### Option 1: Via Seeder/Migration (Recommended)
```php
// database/seeders/CustomModuleSeeder.php
Module::create([
    'code' => 'stunting',
    'name' => 'Data Stunting',
    'min_verified' => 5,
]);

ModuleQuestion::create([
    'module_id' => $module->id,
    'code' => 'st001',
    'question' => 'Berat badan anak (kg)',
    'field_type' => 'number',
    'is_required' => true,
]);
```

### Option 2: Via Admin Panel (Dynamic)
```
POST /api/modules
{
  "code": "stunting",
  "name": "Data Stunting",
  "min_verified": 5,
  "questions": [
    {
      "code": "st001",
      "question": "Berat badan anak (kg)",
      "field_type": "number",
      "is_required": true
    }
  ]
}
```

## 🛠️ Setup Instructions

### 1. Install Laravel
```bash
composer create-project laravel/laravel sid-lite
cd sid-lite
```

### 2. Setup Database
```bash
# Edit .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sid_lite
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 3. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 4. Link mockup.html
```bash
cp ../mockup.html public/form.html
```

### 5. Start Server
```bash
php artisan serve
```

### 6. Access
- Form: http://localhost:8000/form.html
- API: http://localhost:8000/api/...

## 📦 Required Packages

```bash
composer require laravel/sanctum
composer require spatie/laravel-permission (optional, untuk RBAC advanced)
```

## 🔥 Features

✅ Dynamic module system (tambah modul tanpa coding)
✅ Flexible questions per modul
✅ JSON storage untuk responses (flexible structure)
✅ Verification scoring system
✅ Search & filter warga
✅ API-first design (bisa digunakan untuk mobile app)
✅ Role-based access control
✅ Audit trail (who submitted, when)
✅ Scalable architecture

## 📊 Keuntungan Desain Ini

1. **No Code Required**: Tambah modul baru via database/API
2. **Maintainable**: Clear separation of concerns
3. **Scalable**: Bisa handle ratusan modul
4. **Flexible**: Questions bisa berbeda-beda tipe
5. **Auditable**: Track siapa input data, kapan
6. **Reusable**: API bisa digunakan untuk mobile/desktop app

---

**Next Steps**:
- Implement migrations
- Create models & relationships
- Build controllers
- Setup routes
- Create seeders with dummy data
