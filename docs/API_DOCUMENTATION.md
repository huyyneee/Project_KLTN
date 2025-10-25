# API Documentation

Tài liệu chi tiết về các API endpoints của hệ thống.

## Base URL

```
http://159.65.2.46:8000/api
```

## Authentication

Hiện tại API không yêu cầu authentication, nhưng có thể thêm JWT token trong tương lai.

## Response Format

Tất cả API responses đều có format chuẩn:

```json
{
  "success": true,
  "message": "Success message",
  "data": {...}
}
```

## Products API

### GET /api/products

Lấy danh sách tất cả sản phẩm.

**Response:**

```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    {
      "id": 1,
      "code": "SP202510043934",
      "name": "Nước Hoa Nam Nữ Calvin Klein One EDT 50ml",
      "price": 926000,
      "description": "...",
      "specifications": {
        "brand": "Calvin Klein",
        "origin": "Mỹ",
        "made_in": "Mỹ",
        "volume": "50ml",
        "skin_type": ""
      },
      "usage": "...",
      "ingredients": "...",
      "category_id": 1,
      "category_name": "Nước Hoa",
      "main_image": "http://159.65.2.46:8000/uploads/products/...",
      "detail_images": ["http://159.65.2.46:8000/uploads/products/..."],
      "created_at": "2025-10-04 06:41:12",
      "updated_at": "2025-10-04 06:41:12"
    }
  ]
}
```

### POST /api/products

Tạo sản phẩm mới.

**Request Body (FormData):**

```
name: string (required)
price: number (required)
description: string
category_id: number (required)
main_image: file
detail_images: file[]
specifications: object
usage: string
ingredients: string
```

**Response:**

```json
{
  "success": true,
  "message": "Product created successfully",
  "data": null
}
```

### PUT /api/products/{id}

Cập nhật sản phẩm.

**Request Body:**

```json
{
  "name": "Updated Product Name",
  "price": 1000000,
  "description": "Updated description"
}
```

### DELETE /api/products/{id}

Xóa sản phẩm (soft delete).

**Response:**

```json
{
  "success": true,
  "message": "Product deleted successfully",
  "data": null
}
```

## Categories API

### GET /api/categories

Lấy danh sách danh mục.

**Response:**

```json
{
  "success": true,
  "message": "Categories retrieved successfully",
  "data": [
    {
      "id": 1,
      "name": "Nước Hoa",
      "description": "Các sản phẩm nước hoa nam, nữ, unisex",
      "created_at": "2025-09-11 16:24:28",
      "updated_at": "2025-10-05 23:10:55"
    }
  ]
}
```

### POST /api/categories

Tạo danh mục mới.

**Request Body:**

```json
{
  "name": "New Category",
  "description": "Category description"
}
```

### PUT /api/categories/{id}

Cập nhật danh mục.

### DELETE /api/categories/{id}

Xóa danh mục.

## Employees API

### GET /api/employees

Lấy danh sách nhân viên.

**Response:**

```json
{
  "success": true,
  "message": "Danh sách nhân viên",
  "data": [
    {
      "id": 1,
      "account_id": 1,
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "address": "123 Đường ABC",
      "birthday": "1990-01-01",
      "gender": "male",
      "email": "nguyenvana@example.com",
      "account_status": "active",
      "account_created": "2025-10-05 16:50:50",
      "created_at": "2025-10-05 16:50:50",
      "updated_at": "2025-10-23 15:31:21"
    }
  ]
}
```

### POST /api/employees

Tạo nhân viên mới.

**Request Body:**

```json
{
  "email": "employee@example.com",
  "password": "password123",
  "full_name": "Nguyễn Văn A",
  "phone": "0123456789",
  "address": "123 Đường ABC",
  "birthday": "1990-01-01",
  "gender": "male"
}
```

**Response:**

```json
{
  "success": true,
  "message": "Tạo nhân viên thành công",
  "data": {
    "id": 1,
    "account_id": 1,
    "email": "employee@example.com",
    "full_name": "Nguyễn Văn A"
  }
}
```

### PUT /api/employees/{id}

Cập nhật nhân viên.

**Request Body:**

```json
{
  "full_name": "Nguyễn Văn B",
  "phone": "0987654321",
  "address": "456 Đường XYZ"
}
```

### DELETE /api/employees/{id}

Xóa nhân viên.

**Response:**

```json
{
  "success": true,
  "message": "Xóa nhân viên thành công",
  "data": null
}
```

## Upload API

### POST /api/upload

Upload hình ảnh đơn.

**Request Body (FormData):**

```
image: file
```

**Response:**

```json
{
  "success": true,
  "message": "Image uploaded successfully",
  "data": {
    "url": "http://159.65.2.46:8000/uploads/...",
    "filename": "image.jpg",
    "size": 12345,
    "type": "image/jpeg"
  }
}
```

### POST /api/upload/multiple

Upload nhiều hình ảnh.

**Request Body (FormData):**

```
images[]: file[]
```

**Response:**

```json
{
  "success": true,
  "message": "Images uploaded successfully",
  "data": {
    "images": [
      {
        "url": "http://159.65.2.46:8000/uploads/...",
        "filename": "image1.jpg",
        "size": 12345,
        "type": "image/jpeg"
      }
    ],
    "count": 1
  }
}
```

### DELETE /api/upload/{filename}

Xóa hình ảnh.

**Response:**

```json
{
  "success": true,
  "message": "Image deleted successfully",
  "data": null
}
```

## Dashboard API

### GET /api/dashboard/stats

Lấy thống kê dashboard.

**Response:**

```json
{
  "success": true,
  "message": "Dashboard stats retrieved successfully",
  "data": {
    "products": {
      "total": 100,
      "change": "+10%",
      "change_type": "increase"
    },
    "categories": {
      "total": 15,
      "change": "+2%",
      "change_type": "increase"
    },
    "orders": {
      "total": 500,
      "change": "+25%",
      "change_type": "increase"
    },
    "customers": {
      "total": 200,
      "change": "+15%",
      "change_type": "increase"
    }
  }
}
```

## Error Handling

### Error Response Format

```json
{
  "success": false,
  "message": "Error message",
  "data": null
}
```

### Common Error Codes

- `400` - Bad Request
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Internal Server Error

## Rate Limiting

Hiện tại không có rate limiting, nhưng có thể thêm trong tương lai.

## CORS

API hỗ trợ CORS với các headers:

- `Access-Control-Allow-Origin: *`
- `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`
- `Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With`
