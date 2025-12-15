#!/bin/bash

#############################################
# SID Lite - cURL Commands Collection
# Quick reference for API testing
#############################################

BASE_URL="http://localhost"
API_URL="$BASE_URL/api/v1"

echo "=========================================="
echo "📚 SID Lite - cURL Commands Collection"
echo "=========================================="
echo ""
echo "Base URL: $BASE_URL"
echo "API URL: $API_URL"
echo ""
echo "Copy and paste these commands to test the API"
echo ""

cat <<'EOF'
#============================================
# AUTHENTICATION
#============================================

# Login (Get Bearer Token)
curl -X POST http://localhost/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sid.com",
    "password": "password"
  }' | jq '.'

# Save token to variable
TOKEN=$(curl -s -X POST http://localhost/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sid.com",
    "password": "password"
  }' | jq -r '.data.token')

echo "Token: $TOKEN"

# Logout
curl -X POST http://localhost/auth/logout \
  -H "Authorization: Bearer $TOKEN" | jq '.'


#============================================
# MODULES
#============================================

# Get all active modules
curl -X GET http://localhost/api/v1/modules | jq '.'

# Get specific module with questions (Jamban)
curl -X GET http://localhost/api/v1/modules/jamban | jq '.'

# Get specific module with questions (RTLH)
curl -X GET http://localhost/api/v1/modules/rtlh | jq '.'

# Get specific module with questions (PAH)
curl -X GET http://localhost/api/v1/modules/pah | jq '.'

# Create new module (Admin only)
curl -X POST http://localhost/api/v1/modules \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "code": "stunting",
    "name": "Data Stunting",
    "description": "Pemantauan stunting balita",
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
      },
      {
        "code": "st003",
        "question": "Tinggi badan (cm)",
        "field_type": "number",
        "is_required": true
      }
    ]
  }' | jq '.'

# Update module
curl -X PUT http://localhost/api/v1/modules/stunting \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Data Stunting (Updated)",
    "description": "Pemantauan stunting balita - Updated",
    "min_verified": 3,
    "is_active": true
  }' | jq '.'

# Delete (deactivate) module
curl -X DELETE http://localhost/api/v1/modules/stunting | jq '.'


#============================================
# WARGA
#============================================

# Search all warga
curl -X GET http://localhost/api/v1/warga/search | jq '.'

# Search by name
curl -X GET 'http://localhost/api/v1/warga/search?q=Siti' | jq '.'

# Search by NIK
curl -X GET 'http://localhost/api/v1/warga/search?q=3173' | jq '.'

# Search by name (case insensitive)
curl -X GET 'http://localhost/api/v1/warga/search?q=budi' | jq '.'

# Search by Dusun
curl -X GET 'http://localhost/api/v1/warga/search?dusun=Dusun+I' | jq '.'

# Search by RW
curl -X GET 'http://localhost/api/v1/warga/search?rw=02' | jq '.'

# Search by RT
curl -X GET 'http://localhost/api/v1/warga/search?rt=01' | jq '.'

# Combined search (Dusun + RW + RT)
curl -X GET 'http://localhost/api/v1/warga/search?dusun=Dusun+I&rw=02&rt=01' | jq '.'

# Get single warga by NIK
curl -X GET http://localhost/api/v1/warga/3173010101010001 | jq '.'

# Create new warga
curl -X POST http://localhost/api/v1/warga \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010099",
    "nama": "Test User Baru",
    "dusun": "Dusun I",
    "rw": "01",
    "rt": "01",
    "alamat": "Jl. Test No. 123",
    "no_kk": "3173010101000099",
    "tanggal_lahir": "1990-01-01",
    "jenis_kelamin": "L",
    "telepon": "081234567890"
  }' | jq '.'

# Update warga
curl -X PUT http://localhost/api/v1/warga/3173010101010099 \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Test User Updated",
    "dusun": "Dusun II",
    "rw": "03",
    "rt": "02",
    "alamat": "Jl. Updated No. 456"
  }' | jq '.'

# Delete warga (soft delete)
curl -X DELETE http://localhost/api/v1/warga/3173010101010099 | jq '.'


#============================================
# RESPONSES
#============================================

# Save response for Jamban module (draft)
curl -X POST http://localhost/api/v1/responses \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010001",
    "module_code": "jamban",
    "responses": {
      "b3r301a": "1",
      "b3r309a": "1"
    },
    "submit": false
  }' | jq '.'

# Save response for Jamban module (submit final)
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

# Save response for RTLH module
curl -X POST http://localhost/api/v1/responses \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010002",
    "module_code": "rtlh",
    "responses": {
      "b3r301a": "1",
      "b3r303": "2",
      "b3r304": "1",
      "b3r305": "2"
    },
    "submit": true
  }' | jq '.'

# Save response for PAH module
curl -X POST http://localhost/api/v1/responses \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010003",
    "module_code": "pah",
    "responses": {
      "b3r301a": "1",
      "b3r306a": "3"
    },
    "submit": true
  }' | jq '.'

# Get all responses for a warga
curl -X GET http://localhost/api/v1/warga/3173010101010001/responses | jq '.'

# Get response for specific module
curl -X GET http://localhost/api/v1/warga/3173010101010001/responses/jamban | jq '.'


#============================================
# DASHBOARD & STATISTICS
#============================================

# Get dashboard statistics
curl -X GET http://localhost/api/v1/dashboard/stats | jq '.'


#============================================
# BATCH OPERATIONS (For Testing)
#============================================

# Create multiple warga at once
for i in {10..15}; do
  curl -X POST http://localhost/api/v1/warga \
    -H "Content-Type: application/json" \
    -d "{
      \"nik\": \"31730101010100$i\",
      \"nama\": \"Warga Test $i\",
      \"dusun\": \"Dusun I\",
      \"rw\": \"01\",
      \"rt\": \"0$((i % 3 + 1))\",
      \"alamat\": \"Jl. Test No. $i\"
    }"
  echo ""
done

# Save responses for multiple warga
for nik in 3173010101010001 3173010101010002 3173010101010003; do
  curl -X POST http://localhost/api/v1/responses \
    -H "Content-Type: application/json" \
    -d "{
      \"nik\": \"$nik\",
      \"module_code\": \"jamban\",
      \"responses\": {
        \"b3r301a\": \"1\",
        \"b3r309a\": \"1\",
        \"b3r309b\": \"2\",
        \"b3r310\": \"1\"
      },
      \"submit\": true
    }"
  echo ""
done


#============================================
# PERFORMANCE TESTING
#============================================

# Test response time for modules endpoint
time curl -s http://localhost/api/v1/modules > /dev/null

# Test response time for search
time curl -s 'http://localhost/api/v1/warga/search?q=test' > /dev/null

# Test concurrent requests (requires parallel tool)
# Install: sudo apt install parallel
# seq 10 | parallel -j10 "curl -s http://localhost/api/v1/modules > /dev/null"


#============================================
# ERROR HANDLING TESTS
#============================================

# Test with invalid NIK (should return 404)
curl -X GET http://localhost/api/v1/warga/9999999999999999 | jq '.'

# Test with invalid module code (should return 404)
curl -X GET http://localhost/api/v1/modules/invalid_code | jq '.'

# Test save response with missing fields (should return 422)
curl -X POST http://localhost/api/v1/responses \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010001"
  }' | jq '.'

# Test create warga with duplicate NIK (should return 422)
curl -X POST http://localhost/api/v1/warga \
  -H "Content-Type: application/json" \
  -d '{
    "nik": "3173010101010001",
    "nama": "Duplicate"
  }' | jq '.'


#============================================
# EXPORT & IMPORT (Future Features)
#============================================

# TODO: Export data to JSON
# curl -X GET http://localhost/api/v1/export/warga > warga_backup.json

# TODO: Export to CSV
# curl -X GET http://localhost/api/v1/export/warga?format=csv > warga.csv

# TODO: Import from CSV
# curl -X POST http://localhost/api/v1/import/warga \
#   -F "file=@warga.csv"


#============================================
# USEFUL ONE-LINERS
#============================================

# Count total warga
curl -s http://localhost/api/v1/warga/search | jq '.count'

# Get all module codes
curl -s http://localhost/api/v1/modules | jq '.data[].code'

# Get verification rate
curl -s http://localhost/api/v1/dashboard/stats | jq '.data.overview.verification_rate'

# Get warga names only
curl -s http://localhost/api/v1/warga/search | jq '.data[].nama'

# Get all NIK numbers
curl -s http://localhost/api/v1/warga/search | jq '.data[].nik'

# Check if specific warga has response for jamban
curl -s http://localhost/api/v1/warga/3173010101010001/responses/jamban | jq '.data.is_verified'


#============================================
# DEBUG & TROUBLESHOOTING
#============================================

# Verbose output (show headers and request)
curl -v http://localhost/api/v1/modules

# Show only headers
curl -I http://localhost/api/v1/modules

# Show response time
curl -w "\nTime: %{time_total}s\n" -s http://localhost/api/v1/modules > /dev/null

# Follow redirects
curl -L http://localhost/dashboard

# Save response to file
curl http://localhost/api/v1/modules > response.json

# Pretty print JSON without jq
curl http://localhost/api/v1/modules | python3 -m json.tool


#============================================
# NOTES
#============================================

# Prerequisites:
# - Install jq: sudo apt install jq
# - Server running at http://localhost
# - Database seeded with initial data

# Tips:
# - Use jq for JSON formatting: | jq '.'
# - Use -s flag for silent mode: curl -s
# - Use -v flag for verbose output: curl -v
# - Use -I flag for headers only: curl -I
# - Use -w flag for timing: curl -w "\nTime: %{time_total}s\n"

# For more information:
# - See scripts/README.md
# - See LARAVEL_STRUCTURE.md
# - See SETUP_MYSQL_AUTH.md

EOF

echo ""
echo "=========================================="
echo "✅ cURL Examples Ready!"
echo "=========================================="
echo ""
echo "💡 Tips:"
echo "   - Copy commands from above and paste in terminal"
echo "   - Install jq for JSON formatting: sudo apt install jq"
echo "   - Use -s for silent, -v for verbose, -I for headers"
echo ""
