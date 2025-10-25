# Setup Guide

Hướng dẫn cài đặt và cấu hình hệ thống Product Craft Panel.

## 📋 Yêu cầu hệ thống

### Backend (PHP)

- **PHP 8.0+** - Ngôn ngữ lập trình
- **MySQL 8.0+** - Cơ sở dữ liệu
- **Apache/Nginx** - Web server
- **Composer** - Dependency manager
- **PDO Extension** - Database connection

### Frontend (React)

- **Node.js 18+** - JavaScript runtime
- **npm/yarn** - Package manager
- **Modern browser** - Chrome, Firefox, Safari, Edge

## 🚀 Cài đặt Backend

### 1. Clone Repository

```bash
git clone <repository-url>
cd Project_KLTN
```

### 2. Cài đặt Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (for frontend)
cd ../product-craft-panel
npm install
```

### 3. Cấu hình Database

#### Tạo Database

```sql
CREATE DATABASE product_craft;
CREATE USER 'product_craft_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON product_craft.* TO 'product_craft_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Cấu hình Connection

```php
// config/config.php
<?php
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'product_craft',
        'username' => 'product_craft_user',
        'password' => 'your_password',
        'charset' => 'utf8mb4'
    ]
];
```

### 4. Setup Database Schema

```bash
# Run database setup
php setup_database.php

# Hoặc import SQL manually
mysql -u product_craft_user -p product_craft < database_schema.sql
```

### 5. Cấu hình Web Server

#### Apache (.htaccess)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx

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
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 6. Cấu hình Permissions

```bash
# Set proper permissions
chmod -R 755 /path/to/Project_KLTN
chmod -R 777 /path/to/Project_KLTN/public/uploads
chown -R www-data:www-data /path/to/Project_KLTN
```

## 🎨 Cài đặt Frontend

### 1. Cấu hình Environment

```bash
cd product-craft-panel

# Create environment file
cp .env.example .env

# Edit environment variables
nano .env
```

```env
# .env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME=Product Craft Panel
```

### 2. Build và Deploy

```bash
# Development
npm run dev

# Production build
npm run build

# Preview production build
npm run preview
```

### 3. Cấu hình API Base URL

```typescript
// src/lib/api.ts
const API_BASE_URL =
  import.meta.env.VITE_API_BASE_URL || "http://localhost:8000/api";
```

## 🐳 Docker Setup (Optional)

### Docker Compose

```yaml
# docker-compose.yml
version: "3.8"
services:
  app:
    build: .
    ports:
      - "8000:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: product_craft
      MYSQL_USER: product_craft_user
      MYSQL_PASSWORD: your_password
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

### Dockerfile

```dockerfile
FROM php:8.0-apache

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy application
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
```

## 🔧 Cấu hình nâng cao

### 1. SSL/HTTPS

```bash
# Install SSL certificate
sudo certbot --apache -d your-domain.com
```

### 2. Caching

```php
// config/cache.php
return [
    'driver' => 'redis',
    'host' => '127.0.0.1',
    'port' => 6379,
    'database' => 0
];
```

### 3. Logging

```php
// config/logging.php
return [
    'level' => 'debug',
    'path' => '/var/log/product_craft.log',
    'max_files' => 30
];
```

## 🧪 Testing Setup

### 1. Backend Tests

```bash
# Run all tests
php test/test_employee_api_local.php
php test/test_autoloader.php
php test/test_connection.php
```

### 2. Frontend Tests

```bash
cd product-craft-panel

# Unit tests
npm test

# E2E tests
npm run test:e2e
```

### 3. API Testing

```bash
# Test API endpoints
curl -X GET "http://localhost:8000/api/employees"
curl -X GET "http://localhost:8000/api/products"
curl -X GET "http://localhost:8000/api/categories"
```

## 📊 Monitoring Setup

### 1. Error Logging

```php
// app/Core/Logger.php
class Logger {
    public static function error($message, $context = []) {
        error_log(date('Y-m-d H:i:s') . " ERROR: " . $message . "\n", 3, '/var/log/product_craft.log');
    }
}
```

### 2. Performance Monitoring

```bash
# Install monitoring tools
sudo apt-get install htop iotop nethogs

# Monitor database
mysql -u root -p -e "SHOW PROCESSLIST;"
```

### 3. Backup Setup

```bash
# Database backup
mysqldump -u product_craft_user -p product_craft > backup_$(date +%Y%m%d).sql

# File backup
tar -czf backup_files_$(date +%Y%m%d).tar.gz /path/to/Project_KLTN
```

## 🚀 Deployment

### 1. Production Environment

```bash
# Set production environment
export APP_ENV=production
export APP_DEBUG=false

# Optimize autoloader
composer dump-autoload --optimize

# Clear cache
php artisan cache:clear
```

### 2. Frontend Build

```bash
cd product-craft-panel

# Production build
npm run build

# Deploy to CDN
aws s3 sync dist/ s3://your-bucket-name --delete
```

### 3. Database Migration

```bash
# Run migrations
php migrate.php

# Seed initial data
php seed.php
```

## 🔒 Security Configuration

### 1. File Permissions

```bash
# Set secure permissions
find /path/to/Project_KLTN -type f -exec chmod 644 {} \;
find /path/to/Project_KLTN -type d -exec chmod 755 {} \;
chmod 600 config/config.php
```

### 2. Database Security

```sql
-- Remove default users
DELETE FROM mysql.user WHERE User='';
DELETE FROM mysql.user WHERE User='root' AND Host NOT IN ('localhost', '127.0.0.1', '::1');
FLUSH PRIVILEGES;
```

### 3. Web Server Security

```apache
# .htaccess security
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

# Prevent directory browsing
Options -Indexes

# Hide PHP version
ServerTokens Prod
```

## 🐛 Troubleshooting

### Common Issues

#### 1. Database Connection Error

```bash
# Check database status
systemctl status mysql

# Test connection
mysql -u product_craft_user -p -h localhost product_craft
```

#### 2. Permission Denied

```bash
# Fix permissions
sudo chown -R www-data:www-data /path/to/Project_KLTN
sudo chmod -R 755 /path/to/Project_KLTN
```

#### 3. API Not Working

```bash
# Check autoloader
php test/test_autoloader.php

# Check API routes
curl -v http://localhost:8000/api/employees
```

#### 4. Frontend Build Error

```bash
# Clear cache
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

### Debug Commands

```bash
# Check PHP version
php -v

# Check MySQL version
mysql --version

# Check Node.js version
node -v
npm -v

# Check web server status
systemctl status apache2
systemctl status nginx
```

## 📞 Support

### Getting Help

- **Documentation** - Xem docs/README.md
- **API Docs** - Xem docs/API_DOCUMENTATION.md
- **Issues** - Tạo issue trên GitHub
- **Email** - kaiser@example.com

### Useful Commands

```bash
# Quick health check
php test/test_connection.php && echo "Backend OK" || echo "Backend Error"

# Test API
curl -s http://localhost:8000/api/products | jq '.success' && echo "API OK" || echo "API Error"

# Check logs
tail -f /var/log/apache2/error.log
tail -f /var/log/product_craft.log
```
