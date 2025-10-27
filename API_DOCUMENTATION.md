# 📚 Tài liệu API - User Management

## 🎯 Tổng quan

API User Management cung cấp các endpoint để quản lý thông tin khách hàng trong hệ thống. API hỗ trợ các chức năng cơ bản như xem danh sách, tìm kiếm, phân trang và lấy thông tin chi tiết.

### Base URL

```
http://localhost/api
```

### Content-Type

```
application/json
```

### Response Format

Tất cả response đều có format chuẩn:

```json
{
  "success": boolean,
  "message": string,
  "data": object|array
}
```

---

## 📋 Danh sách Endpoints

### 1. **GET /users** - Lấy danh sách tất cả khách hàng

#### Mô tả

Lấy danh sách tất cả khách hàng trong hệ thống.

#### Request

```http
GET /api/users
```

#### Response Success (200)

```json
{
  "success": true,
  "message": "Danh sách khách hàng đã được tải thành công",
  "data": [
    {
      "id": 1,
      "account_id": 101,
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "address": "123 Đường ABC, Quận 1, TP.HCM",
      "birthday": "1990-01-15",
      "gender": "Nam",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    },
    {
      "id": 2,
      "account_id": 102,
      "full_name": "Trần Thị B",
      "phone": "0987654321",
      "address": "456 Đường XYZ, Quận 2, TP.HCM",
      "birthday": "1995-05-20",
      "gender": "Nữ",
      "created_at": "2024-01-02 11:00:00",
      "updated_at": "2024-01-02 11:00:00"
    }
  ]
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tải danh sách khách hàng: [error details]"
}
```

---

### 2. **GET /users/{id}** - Lấy thông tin chi tiết khách hàng

#### Mô tả

Lấy thông tin chi tiết của một khách hàng cụ thể theo ID.

#### Request

```http
GET /api/users/1
```

#### Parameters

- `id` (integer, required): ID của khách hàng

#### Response Success (200)

```json
{
  "success": true,
  "message": "Thông tin khách hàng đã được tải thành công",
  "data": {
    "id": 1,
    "account_id": 101,
    "full_name": "Nguyễn Văn A",
    "phone": "0123456789",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "birthday": "1990-01-15",
    "gender": "Nam",
    "created_at": "2024-01-01 10:00:00",
    "updated_at": "2024-01-01 10:00:00"
  }
}
```

#### Response Error (404)

```json
{
  "success": false,
  "message": "Không tìm thấy khách hàng với ID: 1"
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tải thông tin khách hàng: [error details]"
}
```

---

### 3. **GET /users/search** - Tìm kiếm khách hàng

#### Mô tả

Tìm kiếm khách hàng theo tên hoặc số điện thoại.

#### Request

```http
GET /api/users/search?q=Nguyễn
```

#### Query Parameters

- `q` (string, required): Từ khóa tìm kiếm (tên hoặc số điện thoại)

#### Response Success (200)

```json
{
  "success": true,
  "message": "Kết quả tìm kiếm khách hàng",
  "data": [
    {
      "id": 1,
      "account_id": 101,
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "address": "123 Đường ABC, Quận 1, TP.HCM",
      "birthday": "1990-01-15",
      "gender": "Nam",
      "created_at": "2024-01-01 10:00:00",
      "updated_at": "2024-01-01 10:00:00"
    }
  ]
}
```

#### Response Error (400)

```json
{
  "success": false,
  "message": "Vui lòng nhập từ khóa tìm kiếm"
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tìm kiếm khách hàng: [error details]"
}
```

---

### 4. **GET /users/paginated** - Lấy danh sách khách hàng với phân trang

#### Mô tả

Lấy danh sách khách hàng với hỗ trợ phân trang.

#### Request

```http
GET /api/users/paginated?page=1&limit=10
```

#### Query Parameters

- `page` (integer, optional): Số trang (mặc định: 1)
- `limit` (integer, optional): Số bản ghi mỗi trang (mặc định: 10, tối đa: 100)

#### Response Success (200)

```json
{
  "success": true,
  "message": "Danh sách khách hàng với phân trang",
  "data": {
    "users": [
      {
        "id": 1,
        "account_id": 101,
        "full_name": "Nguyễn Văn A",
        "phone": "0123456789",
        "address": "123 Đường ABC, Quận 1, TP.HCM",
        "birthday": "1990-01-15",
        "gender": "Nam",
        "created_at": "2024-01-01 10:00:00",
        "updated_at": "2024-01-01 10:00:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_records": 50,
      "limit": 10,
      "has_next": true,
      "has_prev": false
    }
  }
}
```

#### Response Error (500)

```json
{
  "success": false,
  "message": "Lỗi khi tải danh sách khách hàng: [error details]"
}
```

---

## 🔧 Cấu trúc Dữ liệu

### User Object

```json
{
  "id": integer,           // ID duy nhất của khách hàng
  "account_id": integer,   // ID tài khoản liên kết
  "full_name": string,     // Họ và tên đầy đủ
  "phone": string,         // Số điện thoại
  "address": string,       // Địa chỉ
  "birthday": string,      // Ngày sinh (YYYY-MM-DD)
  "gender": string,        // Giới tính
  "created_at": string,    // Thời gian tạo (YYYY-MM-DD HH:MM:SS)
  "updated_at": string     // Thời gian cập nhật (YYYY-MM-DD HH:MM:SS)
}
```

### Pagination Object

```json
{
  "current_page": integer,    // Trang hiện tại
  "total_pages": integer,     // Tổng số trang
  "total_records": integer,   // Tổng số bản ghi
  "limit": integer,           // Số bản ghi mỗi trang
  "has_next": boolean,        // Có trang tiếp theo
  "has_prev": boolean         // Có trang trước
}
```

---

## ⚠️ Error Codes

| HTTP Status | Mô tả                                                   |
| ----------- | ------------------------------------------------------- |
| 200         | Success                                                 |
| 400         | Bad Request - Dữ liệu đầu vào không hợp lệ              |
| 404         | Not Found - Không tìm thấy tài nguyên                   |
| 405         | Method Not Allowed - Phương thức HTTP không được hỗ trợ |
| 500         | Internal Server Error - Lỗi server                      |

---

## 🧪 Ví dụ sử dụng

### JavaScript (Fetch API)

```javascript
// Lấy danh sách tất cả khách hàng
fetch("http://localhost/api/users")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Danh sách khách hàng:", data.data);
    } else {
      console.error("Lỗi:", data.message);
    }
  });

// Tìm kiếm khách hàng
fetch("http://localhost/api/users/search?q=Nguyễn")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Kết quả tìm kiếm:", data.data);
    }
  });

// Lấy danh sách với phân trang
fetch("http://localhost/api/users/paginated?page=1&limit=5")
  .then((response) => response.json())
  .then((data) => {
    if (data.success) {
      console.log("Khách hàng:", data.data.users);
      console.log("Phân trang:", data.data.pagination);
    }
  });
```

### cURL

```bash
# Lấy danh sách tất cả khách hàng
curl -X GET "http://localhost/api/users" \
  -H "Content-Type: application/json"

# Lấy thông tin khách hàng theo ID
curl -X GET "http://localhost/api/users/1" \
  -H "Content-Type: application/json"

# Tìm kiếm khách hàng
curl -X GET "http://localhost/api/users/search?q=Nguyễn" \
  -H "Content-Type: application/json"

# Lấy danh sách với phân trang
curl -X GET "http://localhost/api/users/paginated?page=1&limit=10" \
  -H "Content-Type: application/json"
```

### PHP

```php
<?php
// Lấy danh sách khách hàng
$response = file_get_contents('http://localhost/api/users');
$data = json_decode($response, true);

if ($data['success']) {
    foreach ($data['data'] as $user) {
        echo "Tên: " . $user['full_name'] . "\n";
        echo "SĐT: " . $user['phone'] . "\n";
    }
} else {
    echo "Lỗi: " . $data['message'] . "\n";
}
?>
```

---

## 🚀 Test API

### Sử dụng script test

Tạo file `test_user_api.php`:

```php
<?php
function testApi($url, $method = 'GET', $data = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

echo "=== TEST USER API ===\n\n";

// Test 1: Lấy danh sách tất cả khách hàng
echo "1. Test GET /api/users\n";
$result = testApi('http://localhost/api/users');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 2: Lấy thông tin chi tiết khách hàng
echo "2. Test GET /api/users/1\n";
$result = testApi('http://localhost/api/users/1');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 3: Tìm kiếm khách hàng
echo "3. Test GET /api/users/search?q=Nguyen\n";
$result = testApi('http://localhost/api/users/search?q=Nguyen');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 4: Lấy danh sách khách hàng với phân trang
echo "4. Test GET /api/users/paginated?page=1&limit=5\n";
$result = testApi('http://localhost/api/users/paginated?page=1&limit=5');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "=== END TEST ===\n";
?>
```

Chạy test:

```bash
php test_user_api.php
```

---

## 📝 Ghi chú

- Tất cả endpoint đều hỗ trợ CORS
- API sử dụng UTF-8 encoding
- Thời gian được trả về theo format MySQL datetime
- Tìm kiếm không phân biệt hoa thường
- Phân trang có giới hạn tối đa 100 bản ghi mỗi trang

---

## 🔄 Cập nhật

- **v1.0** - Phiên bản đầu tiên với các chức năng cơ bản
  - GET /users - Lấy danh sách khách hàng
  - GET /users/{id} - Lấy thông tin chi tiết
  - GET /users/search - Tìm kiếm khách hàng
  - GET /users/paginated - Phân trang danh sách

---

## 🧾 Orders (Admin)

Các endpoint cho quản trị viên nhằm quản lý đơn hàng. Những endpoint này được thiết kế cho giao diện quản trị (admin panel). Hiện tại authentication dùng session-based (phải đăng nhập và `accounts.role` = `admin`).

Base path: `/api`

Endpoints:

### 1. GET /orders

- Mô tả: Lấy danh sách đơn hàng (admin). Hỗ trợ phân trang và lọc theo `status`.
- Request:

```http
GET /api/orders?page=1&limit=20&status=pending
```

- Query parameters:
  - `page` (integer, optional) - trang hiện tại (mặc định 1)
  - `limit` (integer, optional) - số bản ghi/trang (mặc định 20, tối đa 200)
  - `status` (string, optional) - filter theo trạng thái (`pending`, `paid`, `shipped`, `completed`, `cancelled`)

- Response success (200):

```json
{
  "success": true,
  "message": "Orders retrieved",
  "data": {
    "orders": [
      {
        "id": 123,
        "user_id": 45,
        "order_code": "ORD5FA3C...",
        "status": "pending",
        "total_amount": "150000.00",
        "shipping_address": "123 Đường ...",
        "created_at": "2025-10-20 12:00:00",
        "updated_at": "2025-10-20 12:00:00",
        "items": [
          {
            "id": 1,
            "order_id": 123,
            "product_id": 10,
            "quantity": 2,
            "price": "50000.00",
            "product_name": "Sản phẩm A",
            "image_url": "http://localhost:8000/uploads/....jpg"
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 5,
      "total_records": 100,
      "limit": 20
    }
  }
}
```

### 2. GET /orders/{id}

- Mô tả: Lấy chi tiết một đơn hàng theo ID (admin).
- Request:

```http
GET /api/orders/123
```

- Response success (200):

```json
{
  "success": true,
  "message": "Order retrieved",
  "data": {
    "id": 123,
    "user_id": 45,
    "order_code": "ORD5FA3C...",
    "status": "pending",
    "total_amount": "150000.00",
    "shipping_address": "123 Đường ...",
    "created_at": "2025-10-20 12:00:00",
    "updated_at": "2025-10-20 12:00:00",
    "items": [ /* như trên */ ]
  }
}
```

### 3. POST /orders/{id}/approve

- Mô tả: Duyệt/approve một đơn hàng (admin). Hiện tại hành động này sẽ cập nhật `status` của đơn sang `paid`.
- Request:

```http
POST /api/orders/123/approve
Content-Type: application/json
```

- Response success (200):

```json
{
  "success": true,
  "message": "Order approved",
  "data": { /* order object after update */ }
}
```

#### Lưu ý important

- Authentication: endpoints admin yêu cầu session-based auth. Bạn cần đăng nhập tới trang admin để có `$_SESSION['account_id']` và `accounts.role` phải là `admin`. Nếu không, API sẽ trả 401 hoặc 403 JSON.
- Giả định: "approve" = set `status` => `paid`. Nếu bạn muốn đổi thành `shipped` hoặc thêm các trạng thái khác (ví dụ `cancel`), tôi có thể mở rộng API.
- Các endpoint trả về danh sách `items` cho mỗi order, mỗi item có thông tin sản phẩm và đường dẫn ảnh đầy đủ khi có.

## ✅ Ví dụ sử dụng (Admin)

Giả sử bạn đã đăng nhập trong trình duyệt (session cookie). Dưới đây là ví dụ request dùng curl (sử dụng cookie từ trình duyệt).

### cURL (sử dụng cookie file)

```bash
# Lưu cookie khi đăng nhập (ví dụ):
# curl -c cookies.txt -d "email=admin@example.com&password=..." http://localhost/login

# Lấy danh sách đơn hàng (admin)
curl -b cookies.txt "http://localhost/api/orders?page=1&limit=20"

# Lấy chi tiết 1 đơn
curl -b cookies.txt "http://localhost/api/orders/123"

# Duyệt đơn
curl -X POST -b cookies.txt "http://localhost/api/orders/123/approve"
```

### JavaScript (fetch) - khi client chạy cùng domain và share session cookie

```javascript
fetch('/api/orders?page=1&limit=20', { credentials: 'same-origin' })
  .then(r => r.json())
  .then(console.log);

fetch('/api/orders/123', { credentials: 'same-origin' })
  .then(r => r.json())
  .then(console.log);

fetch('/api/orders/123/approve', { method: 'POST', credentials: 'same-origin' })
  .then(r => r.json())
  .then(console.log);
```

### PHP (test script)

Bạn có thể reuse mẫu `test_user_api.php` để gọi các endpoint trên (sử dụng curl với cookie) — lưu ý cần đăng nhập admin trước và lưu cookie vào file `cookies.txt`.

---

## 🔄 Cập nhật

- **v1.1** - Thêm admin orders API
  - GET /orders - danh sách đơn hàng (admin)
  - GET /orders/{id} - chi tiết đơn hàng (admin)
  - POST /orders/{id}/approve - duyệt đơn (admin)

