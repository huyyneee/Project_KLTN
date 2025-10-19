<?php
// Database setup script
require_once __DIR__ . '/app/Core/Database.php';

try {
  $database = new Database();
  $conn = $database->getConnection();

  if (!$conn) {
    throw new Exception('Failed to connect to database');
  }

  echo "Connected to database successfully!\n";

  // Read and execute the SQL schema
  $sqlFile = __DIR__ . '/hasaki-2.sql';

  if (!file_exists($sqlFile)) {
    // Create the SQL content directly
    $sqlContent = "
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lasted_login` DATETIME,
  `role` ENUM('user','admin','employee') DEFAULT 'user' COMMENT 'user = khách hàng, admin = quản trị, employee = nhân viên',
  `status` ENUM('active','inactive','banned') DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `account_id` INT UNIQUE NOT NULL,
  `full_name` VARCHAR(100),
  `phone` VARCHAR(20),
  `address` VARCHAR(255),
  `birthday` DATE,
  `gender` ENUM('male','female','other'),
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_users_account FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`)
);

CREATE TABLE IF NOT EXISTS `address` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `phone` VARCHAR(100),
  `receiver_name` VARCHAR(100),
  `street` VARCHAR(255),
  `ward` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `province` VARCHAR(100),
  `type` ENUM('home','work','other') DEFAULT 'home',
  `is_default` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_address_user FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) UNIQUE NOT NULL,
  `description` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` CHAR(36) UNIQUE NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `description` TEXT,
  `specifications` TEXT,
  `usage` TEXT,
  `ingredients` TEXT,
  `category_id` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_products_category FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
);

CREATE TABLE IF NOT EXISTS `product_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `url` VARCHAR(500) NOT NULL,
  `is_main` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_product_images_product FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);

CREATE TABLE IF NOT EXISTS `carts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNIQUE NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_carts_user FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `cart_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `cart_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(15,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_cart_items_cart FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  CONSTRAINT fk_cart_items_product FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);

CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `order_code` CHAR(36) UNIQUE NOT NULL,
  `status` ENUM('pending','paid','shipped','completed','cancelled') DEFAULT 'pending',
  `total_amount` DECIMAL(15,2) NOT NULL,
  `shipping_address` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(15,2) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_order_items_order FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT fk_order_items_product FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
);

-- Indexes để tối ưu query
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_cart_items_cart ON cart_items(cart_id);
CREATE INDEX IF NOT EXISTS idx_order_items_order ON order_items(order_id);
CREATE INDEX IF NOT EXISTS idx_orders_user ON orders(user_id);

-- Insert sample categories
INSERT IGNORE INTO `categories` (`name`, `description`) VALUES 
('Sữa rửa mặt', 'Các sản phẩm sữa rửa mặt cho mọi loại da'),
('Kem chống nắng', 'Kem chống nắng bảo vệ da khỏi tia UV'),
('Serum', 'Serum dưỡng da chuyên sâu'),
('Nước hoa', 'Các loại nước hoa nam và nữ'),
('Nước tẩy trang', 'Nước tẩy trang làm sạch da'),
('Sản phẩm khác', 'Các sản phẩm chăm sóc da khác');
        ";

    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sqlContent)));

    foreach ($statements as $statement) {
      if (!empty($statement)) {
        try {
          $conn->exec($statement);
          echo "Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (PDOException $e) {
          echo "Error executing statement: " . $e->getMessage() . "\n";
        }
      }
    }
  }

  echo "\nDatabase setup completed successfully!\n";

} catch (Exception $e) {
  echo "Error: " . $e->getMessage() . "\n";
}
?>