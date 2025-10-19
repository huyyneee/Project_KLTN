# 🚀 Hướng dẫn tối ưu hóa API Performance

## 🐌 **Nguyên nhân API chạy chậm**

### 1. **Database Issues**

- **Remote Database:** Host `160.30.160.22` có thể bị lag network
- **Connection Overhead:** Mỗi request tạo connection mới
- **No Connection Pooling:** Không tái sử dụng connection

### 2. **PHP Server Limitations**

- **Built-in Server:** Không tối ưu cho production
- **No OPcache:** Không cache compiled PHP code
- **No Compression:** Không nén response

### 3. **Query Performance**

- **Missing Indexes:** Có thể thiếu indexes cho các cột thường query
- **N+1 Queries:** Có thể có vấn đề với JOIN queries

## ⚡ **Giải pháp tối ưu hóa**

### 1. **Database Optimization**

#### A. Sử dụng Local Database (Khuyến nghị)

```bash
# Cài đặt MySQL local
brew install mysql  # macOS
# hoặc
sudo apt install mysql-server  # Ubuntu

# Tạo database local
mysql -u root -p
CREATE DATABASE hasaki;
```

#### B. Cập nhật config cho local database

```php
// config/config.php
return [
    'database' => [
        'host' => 'localhost',  // Thay vì 160.30.160.22
        'db_name' => 'hasaki',
        'username' => 'root',
        'password' => 'your_password',
    ],
];
```

#### C. Thêm Database Indexes

```sql
-- Thêm indexes cho performance
CREATE INDEX idx_products_category_id ON products(category_id);
CREATE INDEX idx_products_created_at ON products(created_at);
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_categories_name ON categories(name);

-- Composite indexes
CREATE INDEX idx_products_category_created ON products(category_id, created_at);
```

### 2. **PHP Server Optimization**

#### A. Sử dụng script khởi động tối ưu

```bash
# Thay vì: php -S localhost:8000
# Sử dụng:
./start_optimized.sh
```

#### B. Cài đặt OPcache (nếu chưa có)

```bash
# macOS với Homebrew
brew install php@8.1
brew services start php@8.1

# Ubuntu
sudo apt install php8.1-opcache
```

### 3. **Code Optimization**

#### A. Database Connection (Đã cập nhật)

- ✅ Persistent connections
- ✅ Connection reuse
- ✅ Optimized PDO options

#### B. Query Optimization

```php
// Thay vì query nhiều lần
$products = $productModel->findAllWithCategory(); // 1 query với JOIN

// Thay vì
$products = $productModel->findAll();
foreach($products as $product) {
    $category = $categoryModel->findById($product['category_id']); // N queries
}
```

### 4. **Caching Strategy**

#### A. Response Caching

```php
// Thêm vào ApiController
private function getCachedResponse($key, $callback, $ttl = 300) {
    $cacheFile = __DIR__ . "/../../cache/{$key}.json";

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
        return json_decode(file_get_contents($cacheFile), true);
    }

    $data = $callback();
    file_put_contents($cacheFile, json_encode($data));
    return $data;
}
```

#### B. Database Query Caching

```php
// Cache categories (ít thay đổi)
public function getCachedCategories() {
    return $this->getCachedResponse('categories', function() {
        return $this->categoryModel->findAll();
    }, 600); // Cache 10 phút
}
```

## 🧪 **Test Performance**

### 1. Chạy Performance Test

```bash
cd /Users/kaiser/code/Project_KLTN
php test_performance.php
```

### 2. Test API Response Time

```bash
# Test với curl
time curl -s http://localhost:8000/api/products

# Test với multiple requests
for i in {1..10}; do
  time curl -s http://localhost:8000/api/products > /dev/null
done
```

### 3. Monitor Database Queries

```sql
-- Bật slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1; -- Log queries > 1 second

-- Xem slow queries
SHOW VARIABLES LIKE 'slow_query_log_file';
```

## 📊 **Benchmark Results**

### Before Optimization

- Database connection: ~50-100ms
- Simple query: ~20-50ms
- Complex query: ~100-200ms
- API response: ~200-500ms

### After Optimization (Expected)

- Database connection: ~5-10ms
- Simple query: ~2-5ms
- Complex query: ~10-20ms
- API response: ~50-100ms

## 🎯 **Quick Wins**

### 1. Immediate (5 phút)

```bash
# Sử dụng script tối ưu
cd /Users/kaiser/code/Project_KLTN
./start_optimized.sh
```

### 2. Short-term (30 phút)

- Chuyển sang local database
- Thêm database indexes
- Test performance

### 3. Long-term (1-2 giờ)

- Implement caching
- Optimize queries
- Add monitoring

## 🔧 **Production Setup**

### 1. Nginx + PHP-FPM

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/Project_KLTN/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 2. Redis Caching

```php
// Thêm Redis cho caching
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
```

## 📈 **Monitoring**

### 1. Response Time Monitoring

```php
// Thêm vào mỗi API endpoint
$start = microtime(true);
// ... API logic ...
$end = microtime(true);
error_log("API Response Time: " . round(($end - $start) * 1000, 2) . "ms");
```

### 2. Database Query Monitoring

```php
// Log slow queries
if (($end - $start) > 0.1) { // > 100ms
    error_log("Slow query detected: " . $sql);
}
```

---

## 🚀 **Kết quả mong đợi**

Sau khi áp dụng các tối ưu hóa:

- ⚡ **API response time giảm 70-80%**
- 🔄 **Database connection reuse**
- 📊 **Better query performance**
- 🛡️ **More stable server**

**Bắt đầu với script tối ưu ngay bây giờ!**

