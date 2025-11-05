# 📚 API Documentation - E-commerce System

## 🎯 Tổng quan

Hệ thống API được xây dựng bằng PHP thuần với kiến trúc MVC, cung cấp các endpoint để quản lý sản phẩm, danh mục, đơn hàng, người dùng và dashboard cho hệ thống thương mại điện tử.

## ⚙️ Cấu hình

### 1. Database

### API Base URL
```
http://localhost:8000/api
```

### Content-Type
```
application/json
```

```
http://localhost:8000/api
```

**Response Error (401):**
```json
{
  "success": false,
  "message": "Invalid password",
  "errors": null
}
```

### POST /logout
Đăng xuất khỏi hệ thống

**Response Success (200):**
```json
{
  "success": true,
  "message": "Logout successful",
  "data": null
}
```

---

## 📦 Orders API (Admin Only)

> **Lưu ý**: Tất cả API Orders yêu cầu đăng nhập với role `admin`

### GET /orders
Lấy danh sách đơn hàng với phân trang và lọc

**Query Parameters:**
- `page` (integer, optional): Số trang (mặc định: 1)
- `limit` (integer, optional): Số bản ghi mỗi trang (mặc định: 20, tối đa: 200)
- `status` (string, optional): Lọc theo trạng thái (`pending`, `paid`, `shipped`, `completed`, `cancelled`)

**Request:**
```http
GET /api/orders?page=1&limit=20&status=pending
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Orders retrieved",
  "data": {
    "orders": [
      {
        "id": 30,
        "user_id": 5,
        "order_code": "ORD68FF91B8D01",
        "status": "pending",
        "total_amount": "399000.00",
        "receiver_name": "Huy BakuGa1",
        "receiver_phone": "0398262504",
        "shipping_address": "số 470, Trần Đại Nghĩa...",
        "payment_method": "cod",
        "created_at": "2025-10-27 22:37:28",
        "updated_at": "2025-10-27 22:37:28",
        "items": [
          {
            "id": 46,
            "order_id": 30,
            "product_id": 46,
            "quantity": 1,
            "price": "399000.00",
            "product_name": "Nước Hoa Nữ Laura Anne Diamond Femme 45ml",
            "product_code": "SP202510058767",
            "image_url": "http://159.65.2.46:8000/uploads/products/68e2fc87836df_1759706247_0.jpg"
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "total_pages": 1,
      "total_records": 7,
      "limit": 20
    }
  }
}
```

### GET /orders/{id}
Lấy chi tiết một đơn hàng theo ID

**Request:**
```http
GET /api/orders/30
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order retrieved",
  "data": {
    "id": 30,
    "user_id": 5,
    "order_code": "ORD68FF91B8D01",
    "status": "pending",
    "total_amount": "399000.00",
    "receiver_name": "Huy BakuGa1",
    "receiver_phone": "0398262504",
    "shipping_address": "số 470, Trần Đại Nghĩa...",
    "payment_method": "cod",
    "created_at": "2025-10-27 22:37:28",
    "updated_at": "2025-10-27 22:37:28",
    "items": [
      {
        "id": 46,
        "order_id": 30,
        "product_id": 46,
        "quantity": 1,
        "price": "399000.00",
        "product_name": "Nước Hoa Nữ Laura Anne Diamond Femme 45ml",
        "product_code": "SP202510058767",
        "image_url": "http://159.65.2.46:8000/uploads/products/68e2fc87836df_1759706247_0.jpg"
      }
    ]
  }
}
```

### PATCH /orders/{id}/status
Cập nhật trạng thái đơn hàng theo stepper

Quy tắc chuyển trạng thái:
- `pending` → `paid` | `cancelled`
- `paid` → `shipped` | `cancelled`
- `shipped` → `completed`
- `completed`, `cancelled` → không cho đổi

**Request:**
```http
PATCH /api/orders/30/status
Content-Type: application/json

{
  "status": "paid | shipped | completed | cancelled"
}
```

**Response Success (200):**
```json
{
  "success": true,
  "message": "Order status updated",
  "data": {
    "id": 30,
    "status": "paid",
    "updated_at": "2025-10-28 10:30:00",
    "items": [...]
  }
}
```

**Response Error (400):**
```json
{
  "success": false,
  "message": "Invalid status transition",
  "errors": null
}
```

---

## 👥 Users API

### GET /users
Lấy danh sách tất cả người dùng

**Response Success (200):**
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
    }
  ]
}
```

### GET /users/{id}
Lấy thông tin chi tiết người dùng theo ID

### GET /users/search
Tìm kiếm người dùng theo tên hoặc số điện thoại

**Query Parameters:**
- `q` (string, required): Từ khóa tìm kiếm

**Request:**
```http
GET /api/users/search?q=Nguyễn
```

### GET /users/paginated
Lấy danh sách người dùng với phân trang

**Query Parameters:**
- `page` (integer, optional): Số trang (mặc định: 1)
- `limit` (integer, optional): Số bản ghi mỗi trang (mặc định: 10, tối đa: 100)

---

## 📊 Dashboard API

### GET /dashboard/stats
Lấy thống kê tổng quan dashboard

**Response Success (200):**
```json
{
  "success": true,
  "message": "Dashboard stats retrieved successfully",
  "data": {
    "products": {
      "total": 1234,
      "change": "+12%",
      "change_type": "increase"
    },
    "categories": {
      "total": 56,
      "change": "+3%",
      "change_type": "increase"
    },
    "orders": {
      "total": 890,
      "change": "+25%",
      "change_type": "increase"
    },
    "customers": {
      "total": 2345,
      "change": "+18%",
      "change_type": "increase"
    }
  }
}
```

### GET /dashboard/best-selling
Lấy danh sách sản phẩm bán chạy

```json
{
  "success": true,
  "message": "Best selling products retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Sản phẩm #1",
      "category": "Danh mục A",
      "sold": 123,
      "change": "+15%"
    }
  ]
}
```

#### GET /dashboard/recent-activity

Lấy hoạt động gần đây

```json
{
  "success": true,
  "message": "Recent activity retrieved successfully",
  "data": [
    {
      "id": 1,
      "action": "Thêm sản phẩm mới",
      "time": "2 phút trước",
      "type": "create"
    }
  ]
}
```

## 🛍️ Products API

### GET /products
Lấy danh sách tất cả sản phẩm

**Response Success (200):**
```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "code": "SP001",
      "name": "Kem dưỡng ẩm",
      "price": 299000,
      "description": "<p>Mô tả sản phẩm</p>",
      "specifications": {
        "brand": "Brand A",
        "origin": "Hàn Quốc",
        "made_in": "Việt Nam",
        "volume": "50ml",
        "skin_type": "Mọi loại da"
      },
      "usage": "<p>Hướng dẫn sử dụng</p>",
      "ingredients": "<p>Thành phần</p>",
      "category_id": 1,
      "category_name": "Chăm sóc da",
      "main_image": "image_url",
      "detail_images": ["image1.jpg", "image2.jpg"],
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  ]
}
```

### GET /products/{id}
Lấy sản phẩm theo ID

### POST /products
Tạo sản phẩm mới

**Request:**
```json
{
  "name": "Tên sản phẩm",
  "price": 299000,
  "category_id": 1,
  "description": "<p>Mô tả sản phẩm</p>",
  "specifications": {
    "brand": "Brand A",
    "origin": "Hàn Quốc",
    "made_in": "Việt Nam",
    "volume": "50ml",
    "skin_type": "Mọi loại da"
  },
  "usage": "<p>Hướng dẫn sử dụng</p>",
  "ingredients": "<p>Thành phần</p>",
  "main_image": "image_url",
  "detail_images": ["image1.jpg", "image2.jpg"]
}
```

### PUT /products/{id}
Cập nhật sản phẩm

### DELETE /products/{id}
Xóa sản phẩm

### GET /products/category/{categoryId}
Lấy sản phẩm theo danh mục

---

## 📂 Categories API

### GET /categories
Lấy danh sách tất cả danh mục

**Response Success (200):**
```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Chăm sóc da",
      "description": "Các sản phẩm chăm sóc da",
      "created_at": "2024-01-15 10:30:00",
      "updated_at": "2024-01-15 10:30:00"
    }
  ]
}
```

### GET /categories/{id}
Lấy danh mục theo ID

### POST /categories
Tạo danh mục mới

**Request:**
```json
{
  "name": "Tên danh mục",
  "description": "Mô tả danh mục"
}
```

### PUT /categories/{id}
Cập nhật danh mục

### DELETE /categories/{id}
Xóa danh mục

---

## 🖼️ Image Upload API

### POST /upload
Upload một hình ảnh

**Request:** `multipart/form-data`
- `file`: File hình ảnh

### POST /upload/multiple
Upload nhiều hình ảnh

**Request:** `multipart/form-data`
- `files[]`: Mảng file hình ảnh

### DELETE /upload/{filename}
Xóa hình ảnh

---

## 🧪 Testing API

### Sử dụng file test có sẵn

**Test hoàn chỉnh Orders API:**
```bash
php test_order_complete.php
```

**Test cơ bản:**
```bash
php test_order_api.php
```

### Bảng categories

**Lấy danh sách orders:**
```bash
curl -X GET -H "Content-Type: application/json" \
  -b cookies.txt \
  http://localhost:8000/api/orders
```

### Sử dụng JavaScript (Fetch API)

```javascript
// Login
const loginResponse = await fetch('http://localhost:8000/api/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  credentials: 'include',
  body: JSON.stringify({
    email: '',
    password: ''
  })
});

// Lấy danh sách orders
const ordersResponse = await fetch('http://localhost:8000/api/orders', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json',
  },
  credentials: 'include'
});
```

---

## ⚠️ Error Handling

Tất cả API đều trả về response theo format chuẩn:

**Success Response:**
```json
{
  "success": true,
  "message": "Success message",
  "data": { /* response data */ }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Error message",
  "errors": "Additional error details (optional)"
}
```

**HTTP Status Codes:**
- `200` - Success
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Internal Server Error

---

## 🔧 CORS Configuration

API đã được cấu hình CORS để cho phép frontend gọi API:

- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With`
- `Access-Control-Max-Age: 86400`

---

## 📝 Ghi chú quan trọng

1. **Authentication**: Orders API yêu cầu đăng nhập với role `admin`
2. **Session-based**: Sử dụng PHP session để quản lý authentication
3. **Password Support**: Hỗ trợ cả MD5 và bcrypt password hashing
4. **Image URLs**: Tự động xử lý đường dẫn ảnh sản phẩm
5. **Pagination**: Hỗ trợ phân trang cho tất cả API list
6. **UTF-8**: Tất cả response sử dụng UTF-8 encoding

---

## 🚀 Quick Start

1. **Khởi động server:**
```bash
php -S localhost:8000 -t public
```

2. **Test API:**
```bash
php test_order_complete.php
```

3. **Sử dụng trong frontend:**
```javascript
const API_BASE_URL = "http://localhost:8000/api";
```