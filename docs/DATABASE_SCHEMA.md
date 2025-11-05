# Database Schema

Cấu trúc cơ sở dữ liệu của hệ thống Product Craft Panel.

## 📊 Tổng quan

Hệ thống sử dụng MySQL với các bảng chính:

- **accounts** - Tài khoản người dùng
- **users** - Thông tin nhân viên
- **products** - Sản phẩm
- **categories** - Danh mục sản phẩm
- **product_images** - Hình ảnh sản phẩm
- **orders** - Đơn hàng
- **order_items** - Chi tiết đơn hàng
- **cart** - Giỏ hàng
- **cart_items** - Chi tiết giỏ hàng
- **addresses** - Địa chỉ người dùng

## 🗃️ Chi tiết các bảng

### 1. Bảng `accounts`

Lưu trữ thông tin tài khoản người dùng.

```sql
CREATE TABLE accounts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `UNIQUE KEY email (email)`
- `KEY status (status)`

### 2. Bảng `users`

Lưu trữ thông tin chi tiết nhân viên.

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    account_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    birthday DATE,
    gender ENUM('male', 'female', 'other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `FOREIGN KEY (account_id) REFERENCES accounts(id)`

### 3. Bảng `categories`

Lưu trữ danh mục sản phẩm.

```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `KEY deleted_at (deleted_at)`

### 4. Bảng `products`

Lưu trữ thông tin sản phẩm.

```sql
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    specifications JSON,
    usage TEXT,
    ingredients TEXT,
    category_id INT NOT NULL,
    main_image VARCHAR(500),
    detail_images JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `UNIQUE KEY code (code)`
- `KEY category_id (category_id)`
- `KEY deleted_at (deleted_at)`

### 5. Bảng `product_images`

Lưu trữ hình ảnh sản phẩm.

```sql
CREATE TABLE product_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    image_type ENUM('main', 'detail') DEFAULT 'detail',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `KEY product_id (product_id)`
- `KEY image_type (image_type)`

### 6. Bảng `orders`

Lưu trữ thông tin đơn hàng.

```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `UNIQUE KEY order_number (order_number)`
- `KEY user_id (user_id)`
- `KEY status (status)`

### 7. Bảng `order_items`

Lưu trữ chi tiết đơn hàng.

```sql
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `KEY order_id (order_id)`
- `KEY product_id (product_id)`

### 8. Bảng `cart`

Lưu trữ giỏ hàng người dùng.

```sql
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `UNIQUE KEY user_id (user_id)`

### 9. Bảng `cart_items`

Lưu trữ chi tiết giỏ hàng.

```sql
CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `KEY cart_id (cart_id)`
- `KEY product_id (product_id)`

### 10. Bảng `addresses`

Lưu trữ địa chỉ người dùng.

```sql
CREATE TABLE addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(100),
    district VARCHAR(100),
    ward VARCHAR(100),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Indexes:**

- `PRIMARY KEY (id)`
- `KEY user_id (user_id)`
- `KEY is_default (is_default)`

## 🔗 Relationships

### Entity Relationship Diagram

```
accounts (1) -----> (1) users
  |
  v
orders (1) -----> (n) order_items
  |
  v
products (1) -----> (n) order_items

categories (1) -----> (n) products
products (1) -----> (n) product_images

users (1) -----> (1) cart
cart (1) -----> (n) cart_items
products (1) -----> (n) cart_items

users (1) -----> (n) addresses
```

### Foreign Key Constraints

```sql
-- Users -> Accounts
ALTER TABLE users ADD CONSTRAINT fk_users_account_id
FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE;

-- Products -> Categories
ALTER TABLE products ADD CONSTRAINT fk_products_category_id
FOREIGN KEY (category_id) REFERENCES categories(id);

-- Order Items -> Orders
ALTER TABLE order_items ADD CONSTRAINT fk_order_items_order_id
FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE;

-- Order Items -> Products
ALTER TABLE order_items ADD CONSTRAINT fk_order_items_product_id
FOREIGN KEY (product_id) REFERENCES products(id);

-- Orders -> Users
ALTER TABLE orders ADD CONSTRAINT fk_orders_user_id
FOREIGN KEY (user_id) REFERENCES users(id);

-- Cart -> Users
ALTER TABLE cart ADD CONSTRAINT fk_cart_user_id
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Cart Items -> Cart
ALTER TABLE cart_items ADD CONSTRAINT fk_cart_items_cart_id
FOREIGN KEY (cart_id) REFERENCES cart(id) ON DELETE CASCADE;

-- Cart Items -> Products
ALTER TABLE cart_items ADD CONSTRAINT fk_cart_items_product_id
FOREIGN KEY (product_id) REFERENCES products(id);

-- Product Images -> Products
ALTER TABLE product_images ADD CONSTRAINT fk_product_images_product_id
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;

-- Addresses -> Users
ALTER TABLE addresses ADD CONSTRAINT fk_addresses_user_id
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;
```

## 📈 Performance Optimization

### Indexes Strategy

```sql
-- Composite indexes for common queries
CREATE INDEX idx_products_category_deleted ON products(category_id, deleted_at);
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_cart_items_cart ON cart_items(cart_id);

-- Full-text search indexes
ALTER TABLE products ADD FULLTEXT(name, description);
ALTER TABLE categories ADD FULLTEXT(name, description);
```

### Query Optimization

```sql
-- Optimized product listing with category
SELECT p.*, c.name as category_name
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.deleted_at IS NULL
ORDER BY p.created_at DESC;

-- Optimized order with items
SELECT o.*, oi.product_id, oi.quantity, oi.price
FROM orders o
LEFT JOIN order_items oi ON o.id = oi.order_id
WHERE o.user_id = ?
ORDER BY o.created_at DESC;
```

## 🔄 Data Migration

### Sample Data

```sql
-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Nước Hoa', 'Các sản phẩm nước hoa nam, nữ, unisex'),
('Chăm Sóc Da', 'Các sản phẩm chăm sóc da mặt'),
('Trang Điểm', 'Các sản phẩm trang điểm');

-- Insert sample products
INSERT INTO products (code, name, price, description, category_id) VALUES
('SP001', 'Nước Hoa Calvin Klein One EDT 50ml', 926000, 'Nước hoa unisex', 1),
('SP002', 'Kem Dưỡng Ẩm Neutrogena', 250000, 'Kem dưỡng ẩm cho da khô', 2);

-- Insert sample account
INSERT INTO accounts (email, password, status) VALUES
('admin@example.com', MD5('admin123'), 'active');

-- Insert sample user
INSERT INTO users (account_id, full_name, phone, gender) VALUES
(1, 'Admin User', '0123456789', 'male');
```

### Backup Script

```bash
#!/bin/bash
# backup_database.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/database"
DB_NAME="product_craft"
DB_USER="product_craft_user"
DB_PASS="your_password"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/backup_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete

echo "Backup completed: backup_$DATE.sql.gz"
```

## 🔧 Maintenance

### Regular Maintenance Tasks

```sql
-- Optimize tables
OPTIMIZE TABLE products, categories, orders, order_items;

-- Analyze tables for better query planning
ANALYZE TABLE products, categories, orders, order_items;

-- Check table integrity
CHECK TABLE products, categories, orders, order_items;
```

### Monitoring Queries

```sql
-- Check table sizes
SELECT
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables
WHERE table_schema = 'product_craft'
ORDER BY (data_length + index_length) DESC;

-- Check slow queries
SHOW VARIABLES LIKE 'slow_query_log';
SHOW VARIABLES LIKE 'long_query_time';

-- Check connections
SHOW STATUS LIKE 'Threads_connected';
SHOW STATUS LIKE 'Max_used_connections';
```

## 📊 Analytics Queries

### Sales Analytics

```sql
-- Daily sales
SELECT
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(total_amount) as revenue
FROM orders
WHERE status = 'delivered'
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Top selling products
SELECT
    p.name,
    SUM(oi.quantity) as total_sold,
    SUM(oi.total) as total_revenue
FROM order_items oi
JOIN products p ON oi.product_id = p.id
JOIN orders o ON oi.order_id = o.id
WHERE o.status = 'delivered'
GROUP BY p.id, p.name
ORDER BY total_sold DESC
LIMIT 10;
```

### User Analytics

```sql
-- User registration by month
SELECT
    YEAR(created_at) as year,
    MONTH(created_at) as month,
    COUNT(*) as new_users
FROM accounts
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY year DESC;

-- Active users (users with orders in last 30 days)
SELECT COUNT(DISTINCT user_id) as active_users
FROM orders
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```
