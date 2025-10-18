# API Documentation

## Tổng quan

Hệ thống API được xây dựng bằng PHP thuần với kiến trúc MVC, cung cấp các endpoint để quản lý sản phẩm, danh mục và dashboard.

## Cấu hình

### 1. Database

- Host: `160.30.160.22`
- Database: `hasaki`
- Username: `root`
- Password: `n3a85pGmBP,h`

### 2. API Base URL

```
http://localhost:8000/api
```

## API Endpoints

### Dashboard API

#### GET /dashboard/stats

Lấy thống kê tổng quan dashboard

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

#### GET /dashboard/best-selling

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

### Product API

#### GET /products

Lấy danh sách tất cả sản phẩm

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

#### GET /products/{id}

Lấy sản phẩm theo ID

#### POST /products

Tạo sản phẩm mới

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

#### PUT /products/{id}

Cập nhật sản phẩm

#### DELETE /products/{id}

Xóa sản phẩm

#### GET /products/category/{categoryId}

Lấy sản phẩm theo danh mục

### Category API

#### GET /categories

Lấy danh sách tất cả danh mục

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

#### GET /categories/{id}

Lấy danh mục theo ID

#### POST /categories

Tạo danh mục mới

```json
{
  "name": "Tên danh mục",
  "description": "Mô tả danh mục"
}
```

#### PUT /categories/{id}

Cập nhật danh mục

#### DELETE /categories/{id}

Xóa danh mục

## Cấu trúc Database

### Bảng products

```sql
CREATE TABLE products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  code VARCHAR(50),
  name VARCHAR(255) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  description TEXT,
  specifications JSON,
  usage TEXT,
  ingredients TEXT,
  category_id INT,
  main_image VARCHAR(255),
  detail_images JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

### Bảng categories

```sql
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## CORS Configuration

API đã được cấu hình CORS để cho phép frontend React gọi API:

- Access-Control-Allow-Origin: \*
- Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
- Access-Control-Allow-Headers: Content-Type, Authorization

## Error Handling

Tất cả API đều trả về response theo format:

```json
{
  "success": false,
  "message": "Error message",
  "errors": "Additional error details (optional)"
}
```

## Frontend Integration

Frontend React sử dụng axios để gọi API với base URL:

```typescript
const API_BASE_URL = "http://localhost:8000/api";
```

Các hook đã được tạo sẵn:

- `useProducts()` - Quản lý sản phẩm
- `useCategories()` - Quản lý danh mục
- `useDashboard()` - Dashboard data
