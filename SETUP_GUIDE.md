# Hướng dẫn Setup Hệ thống

## Yêu cầu hệ thống

- PHP 7.4+ hoặc PHP 8.x
- MySQL 5.7+ hoặc MySQL 8.x
- Apache/Nginx web server
- Node.js 16+ (cho frontend)
- Composer (cho PHP dependencies)

## Setup Backend (PHP)

### 1. Cấu hình Database

Tạo database và import schema:

```sql
CREATE DATABASE hasaki;
USE hasaki;

-- Tạo bảng categories
CREATE TABLE categories (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tạo bảng products
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

-- Thêm dữ liệu mẫu
INSERT INTO categories (name, description) VALUES
('Chăm sóc da', 'Các sản phẩm chăm sóc da mặt và cơ thể'),
('Trang điểm', 'Các sản phẩm trang điểm và làm đẹp'),
('Nước hoa', 'Các loại nước hoa và nước thơm');

INSERT INTO products (code, name, price, description, category_id) VALUES
('SP001', 'Kem dưỡng ẩm Vitamin C', 299000, '<p>Kem dưỡng ẩm chứa Vitamin C giúp làm sáng da</p>', 1),
('SP002', 'Serum retinol chống lão hóa', 459000, '<p>Serum retinol giúp chống lão hóa hiệu quả</p>', 1),
('SP003', 'Son môi matte cao cấp', 189000, '<p>Son môi matte bền màu, không khô môi</p>', 2);
```

### 2. Cấu hình Database Connection

Cập nhật file `config/config.php`:

```php
<?php
return [
    'database' => [
        'host' => 'localhost', // Thay đổi theo cấu hình của bạn
        'db_name' => 'hasaki',
        'username' => 'root', // Thay đổi theo cấu hình của bạn
        'password' => '', // Thay đổi theo cấu hình của bạn
    ],
];
```

### 3. Setup Web Server

#### Với Apache:

1. Copy project vào thư mục web root
2. Đảm bảo mod_rewrite được bật
3. Cấu hình Virtual Host:

```apache
<VirtualHost *:80>
    DocumentRoot /path/to/Project_KLTN/public
    ServerName localhost:8000
    <Directory /path/to/Project_KLTN/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Với PHP Built-in Server (Development):

```bash
cd /path/to/Project_KLTN/public
php -S localhost:8000
```

### 4. Test API

Kiểm tra API hoạt động:

```bash
curl http://localhost:8000/api/categories
curl http://localhost:8000/api/products
curl http://localhost:8000/api/dashboard/stats
```

## Setup Frontend (React)

### 1. Cài đặt Dependencies

```bash
cd /path/to/product-craft-panel
npm install
```

### 2. Cấu hình API URL

Đảm bảo API_BASE_URL trong `src/lib/api.ts` đúng:

```typescript
const API_BASE_URL = "http://localhost:8000/api";
```

### 3. Chạy Development Server

```bash
npm run dev
```

### 4. Build Production

```bash
npm run build
```

## Troubleshooting

### Lỗi CORS

Nếu gặp lỗi CORS, kiểm tra:

1. Backend đã cấu hình CORS headers
2. API_BASE_URL đúng
3. Browser không block mixed content

### Lỗi Database Connection

1. Kiểm tra thông tin database trong `config/config.php`
2. Đảm bảo MySQL service đang chạy
3. Kiểm tra user có quyền truy cập database

### Lỗi 404 API

1. Kiểm tra .htaccess file
2. Đảm bảo mod_rewrite được bật
3. Kiểm tra URL routing trong `routes/api.php`

### Lỗi Frontend Build

1. Kiểm tra TypeScript errors: `npx tsc --noEmit`
2. Kiểm tra ESLint errors: `npm run lint`
3. Clear cache: `rm -rf node_modules && npm install`

## Production Deployment

### Backend

1. Cấu hình production database
2. Set proper file permissions
3. Cấu hình SSL certificate
4. Enable error logging

### Frontend

1. Build production: `npm run build`
2. Deploy thư mục `dist/` lên web server
3. Cấu hình reverse proxy nếu cần
4. Set proper cache headers

## API Testing

### Test với Postman/Insomnia

Import collection với các endpoints:

- GET `/api/categories`
- POST `/api/categories`
- GET `/api/products`
- POST `/api/products`
- GET `/api/dashboard/stats`

### Test với curl

```bash
# Get categories
curl -X GET http://localhost:8000/api/categories

# Create category
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -d '{"name":"Test Category","description":"Test Description"}'

# Get products
curl -X GET http://localhost:8000/api/products
```

## Monitoring

- Kiểm tra error logs trong web server
- Monitor database performance
- Check API response times
- Monitor frontend bundle size
