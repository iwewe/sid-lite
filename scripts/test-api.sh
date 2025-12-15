#!/bin/bash

#############################################
# SID Lite - API Testing Script
# Description: Test API endpoints with curl
#############################################

BASE_URL="http://localhost"
API_URL="$BASE_URL/api/v1"

echo "=========================================="
echo "🧪 SID Lite - API Testing"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_test() {
    echo ""
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
    echo -e "${YELLOW}Test: $1${NC}"
    echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Test 1: Get all modules
print_test "GET /api/v1/modules - Get all active modules"
echo "Request:"
echo "  curl -X GET $API_URL/modules"
echo ""
echo "Response:"
curl -s -X GET "$API_URL/modules" | jq '.'

# Test 2: Get specific module with questions
print_test "GET /api/v1/modules/jamban - Get Jamban module with questions"
echo "Request:"
echo "  curl -X GET $API_URL/modules/jamban"
echo ""
echo "Response:"
curl -s -X GET "$API_URL/modules/jamban" | jq '.'

# Test 3: Search warga
print_test "GET /api/v1/warga/search - Search warga by name"
echo "Request:"
echo "  curl -X GET '$API_URL/warga/search?q=Siti'"
echo ""
echo "Response:"
curl -s -X GET "$API_URL/warga/search?q=Siti" | jq '.'

# Test 4: Get single warga
print_test "GET /api/v1/warga/{nik} - Get warga by NIK"
echo "Request:"
echo "  curl -X GET $API_URL/warga/3173010101010001"
echo ""
echo "Response:"
curl -s -X GET "$API_URL/warga/3173010101010001" | jq '.'

# Test 5: Save response
print_test "POST /api/v1/responses - Save module response"
echo "Request:"
cat <<EOF
  curl -X POST $API_URL/responses \\
    -H "Content-Type: application/json" \\
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
EOF
echo ""
echo "Response:"
curl -s -X POST "$API_URL/responses" \
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

# Test 6: Get warga responses
print_test "GET /api/v1/warga/{nik}/responses - Get all responses for warga"
echo "Request:"
echo "  curl -X GET $API_URL/warga/3173010101010001/responses"
echo ""
echo "Response:"
curl -s -X GET "$API_URL/warga/3173010101010001/responses" | jq '.'

# Test 7: Get dashboard stats
print_test "GET /api/v1/dashboard/stats - Get dashboard statistics"
echo "Request:"
echo "  curl -X GET $API_URL/dashboard/stats"
echo ""
echo "Response:"
curl -s -X GET "$API_URL/dashboard/stats" | jq '.'

# Test 8: API Authentication
print_test "POST /auth/login - API Login"
echo "Request:"
cat <<EOF
  curl -X POST $BASE_URL/auth/login \\
    -H "Content-Type: application/json" \\
    -d '{
      "email": "admin@sid.com",
      "password": "password"
    }'
EOF
echo ""
echo "Response:"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sid.com",
    "password": "password"
  }')
echo "$LOGIN_RESPONSE" | jq '.'

# Extract token
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token // empty')

if [ ! -z "$TOKEN" ]; then
    print_success "Login successful! Token: ${TOKEN:0:20}..."

    # Test 9: Authenticated API call
    print_test "GET /api/v1/modules - With Bearer Token"
    echo "Request:"
    echo "  curl -X GET $API_URL/modules \\"
    echo "    -H 'Authorization: Bearer $TOKEN'"
    echo ""
    echo "Response:"
    curl -s -X GET "$API_URL/modules" \
      -H "Authorization: Bearer $TOKEN" | jq '.'
else
    print_error "Login failed or token not found"
fi

echo ""
echo "=========================================="
echo "✅ API Testing Complete!"
echo "=========================================="
echo ""
echo "📚 Available Endpoints:"
echo ""
echo "Warga:"
echo "  GET    $API_URL/warga/search?q=...&dusun=...&rw=...&rt=..."
echo "  GET    $API_URL/warga/{nik}"
echo "  POST   $API_URL/warga"
echo "  PUT    $API_URL/warga/{nik}"
echo "  DELETE $API_URL/warga/{nik}"
echo ""
echo "Modules:"
echo "  GET    $API_URL/modules"
echo "  GET    $API_URL/modules/{code}"
echo "  POST   $API_URL/modules"
echo "  PUT    $API_URL/modules/{code}"
echo "  DELETE $API_URL/modules/{code}"
echo ""
echo "Responses:"
echo "  GET    $API_URL/warga/{nik}/responses"
echo "  GET    $API_URL/warga/{nik}/responses/{module_code}"
echo "  POST   $API_URL/responses"
echo "  DELETE $API_URL/responses/{id}"
echo ""
echo "Dashboard:"
echo "  GET    $API_URL/dashboard/stats"
echo ""
echo "Authentication:"
echo "  POST   $BASE_URL/auth/login"
echo "  POST   $BASE_URL/auth/logout (requires Bearer token)"
echo ""
echo "📖 Full documentation: See LARAVEL_STRUCTURE.md"
echo ""
